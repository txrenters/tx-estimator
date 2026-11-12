<?php

use App\Enums\EstimateStatus;
use App\Models\Company;
use App\Models\Estimate;
use App\Models\ServiceTemplate;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function estimatePayload(array $overrides = []): array
{
    return array_replace_recursive([
        'title' => 'Coil replacement',
        'service_type' => 'HVAC repair',
        'client' => 'Jane Roe',
        'address' => '1 Main St, Houston, TX 77002',
        'lines' => [
            ['name' => 'Labor', 'qty' => 2, 'unit' => 'hr', 'labor' => 100, 'materials' => 50, 'fixed' => null],
        ],
        'pricing' => ['markup' => 30, 'discount' => 0, 'trip' => 0, 'authorization' => 250, 'manager' => 1000, 'target' => 25],
        'scope' => 'Replace coil.',
        'terms' => 'Net 30.',
        'notes' => 'Internal only.',
    ], $overrides);
}

beforeEach(function () {
    $this->air = Company::factory()->create(['default_terms' => 'Company default terms.']);
    $this->roof = Company::factory()->create();
});

test('estimator creates an estimate in own company even if company id is forged', function () {
    $me = User::factory()->estimator($this->air)->create();

    $this->actingAs($me)->post(route('estimates.store'), estimatePayload(['company_id' => $this->roof->id]))
        ->assertRedirect();

    $estimate = Estimate::firstOrFail();
    expect($estimate->company_id)->toBe($this->air->id)
        ->and($estimate->user_id)->toBe($me->id)
        ->and($estimate->status)->toBe(EstimateStatus::Draft);
});

test('admin in all companies must supply a company', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('estimates.store'), estimatePayload())->assertSessionHasErrors('company_id');

    $this->actingAs($admin)->post(route('estimates.store'), estimatePayload(['company_id' => $this->roof->id]))->assertRedirect();
    expect(Estimate::firstOrFail()->company_id)->toBe($this->roof->id);
});

test('server totals ignore client sent totals', function () {
    $me = User::factory()->estimator($this->air)->create();

    $this->actingAs($me)->post(route('estimates.store'), estimatePayload(['total' => 1, 'margin' => 99]));

    $estimate = Estimate::firstOrFail();
    expect((float) $estimate->total)->toBe(390.0)
        ->and((float) $estimate->cost)->toBe(300.0)
        ->and((float) $estimate->margin)->toEqualWithDelta(23.0769, 0.0001)
        ->and($estimate->incomplete)->toBeFalse();
});

test('title is required with the original message', function () {
    $me = User::factory()->estimator($this->air)->create();

    $this->actingAs($me)->post(route('estimates.store'), estimatePayload(['title' => '']))
        ->assertSessionHasErrors(['title' => 'Enter an estimate title before saving.']);
});

test('status cannot be set through save', function () {
    $me = User::factory()->estimator($this->air)->create();

    $this->actingAs($me)->post(route('estimates.store'), estimatePayload(['status' => 'approved']));

    expect(Estimate::firstOrFail()->status)->toBe(EstimateStatus::Draft);
});

test('create from template prefills lines and template terms', function () {
    $template = ServiceTemplate::factory()->for($this->air)->create([
        'title' => 'Coil job', 'service_type' => 'HVAC replacement', 'terms' => 'Template terms.',
        'lines' => [['name' => 'Coil labor', 'qty' => 1, 'unit' => 'job', 'labor' => 0, 'materials' => 0, 'fixed' => 1800]],
    ]);

    $this->actingAs(User::factory()->estimator($this->air)->create())
        ->get(route('estimates.create', ['template' => $template->id]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('estimates/Builder')
            ->where('estimate.title', 'Coil job')
            ->where('estimate.service_type', 'HVAC replacement')
            ->where('estimate.terms', 'Template terms.')
            ->where('estimate.lines.0.fixed', 1800)
            ->where('estimate.pricing.markup', 30));
});

test('template without terms falls back to company default terms', function () {
    $template = ServiceTemplate::factory()->for($this->air)->create(['terms' => null]);

    $this->actingAs(User::factory()->estimator($this->air)->create())
        ->get(route('estimates.create', ['template' => $template->id]))
        ->assertInertia(fn (Assert $page) => $page->where('estimate.terms', 'Company default terms.'));
});

test('cannot start from another company template', function () {
    $template = ServiceTemplate::factory()->for($this->roof)->create();

    $this->actingAs(User::factory()->estimator($this->air)->create())
        ->get(route('estimates.create', ['template' => $template->id]))->assertForbidden();
});

test('estimator cannot edit a colleague estimate', function () {
    $estimate = Estimate::factory()->for($this->air)->create();
    $me = User::factory()->estimator($this->air)->create();

    $this->actingAs($me)->put(route('estimates.update', $estimate), estimatePayload())->assertForbidden();
    $this->actingAs($me)->get(route('estimates.edit', $estimate))
        ->assertInertia(fn (Assert $page) => $page->where('can.update', false));
});

test('editing an approved estimate returns it to draft', function () {
    $estimate = Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Approved, 'approved_at' => now()]);

    $this->actingAs(User::factory()->manager($this->air)->create())
        ->put(route('estimates.update', $estimate), estimatePayload())->assertRedirect();

    expect($estimate->fresh()->status)->toBe(EstimateStatus::Draft)
        ->and($estimate->fresh()->approved_at)->toBeNull();
});

test('estimator saving own declined estimate returns it to draft', function () {
    $me = User::factory()->estimator($this->air)->create();
    $estimate = Estimate::factory()->for($this->air)->for($me, 'creator')->create(['status' => EstimateStatus::Declined]);

    $this->actingAs($me)->put(route('estimates.update', $estimate), estimatePayload())->assertRedirect();

    expect($estimate->fresh()->status)->toBe(EstimateStatus::Draft);
});

test('manager editing a pending estimate keeps it pending', function () {
    $estimate = Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Pending]);

    $this->actingAs(User::factory()->manager($this->air)->create())
        ->put(route('estimates.update', $estimate), estimatePayload())->assertRedirect();

    expect($estimate->fresh()->status)->toBe(EstimateStatus::Pending);
});

test('duplicate copies lines with copy title as a draft', function () {
    $estimate = Estimate::factory()->for($this->air)->create(['title' => 'Roof job', 'status' => EstimateStatus::Approved]);
    $me = User::factory()->estimator($this->air)->create();

    $this->actingAs($me)->post(route('estimates.duplicate', $estimate))->assertRedirect();

    $copy = Estimate::where('title', 'Roof job (copy)')->firstOrFail();
    expect($copy->status)->toBe(EstimateStatus::Draft)
        ->and($copy->user_id)->toBe($me->id)
        ->and($copy->number)->not->toBe($estimate->number)
        ->and($copy->lines)->toEqual($estimate->lines);
});

test('estimator cannot delete a pending estimate', function () {
    $me = User::factory()->estimator($this->air)->create();
    $estimate = Estimate::factory()->for($this->air)->for($me, 'creator')->create(['status' => EstimateStatus::Pending]);

    $this->actingAs($me)->delete(route('estimates.destroy', $estimate))->assertForbidden();
});

test('estimator deletes own draft', function () {
    $me = User::factory()->estimator($this->air)->create();
    $estimate = Estimate::factory()->for($this->air)->for($me, 'creator')->create();

    $this->actingAs($me)->delete(route('estimates.destroy', $estimate))->assertRedirect(route('estimates.index'));
    expect($estimate->fresh())->toBeNull();
});

test('cannot open or duplicate another company estimate', function () {
    $estimate = Estimate::factory()->for($this->roof)->create();
    $me = User::factory()->manager($this->air)->create();

    $this->actingAs($me)->get(route('estimates.edit', $estimate))->assertForbidden();
    $this->actingAs($me)->post(route('estimates.duplicate', $estimate))->assertForbidden();
    $this->actingAs($me)->put(route('estimates.update', $estimate), estimatePayload())->assertForbidden();
});

test('an estimate with no lines is rejected', function () {
    $this->actingAs(User::factory()->estimator($this->air)->create())
        ->post(route('estimates.store'), array_merge(estimatePayload(), ['lines' => []]))
        ->assertSessionHasErrors('lines');
});

test('admin with a company selected can still file an estimate under another chosen company', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->withSession(['current_company_id' => $this->air->id])
        ->post(route('estimates.store'), estimatePayload(['company_id' => $this->roof->id]))
        ->assertRedirect();

    expect(Estimate::sole()->company_id)->toBe($this->roof->id);
});

test('admin with a company selected and no explicit choice files under the current company', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->withSession(['current_company_id' => $this->air->id])
        ->post(route('estimates.store'), estimatePayload())
        ->assertRedirect();

    expect(Estimate::sole()->company_id)->toBe($this->air->id);
});
