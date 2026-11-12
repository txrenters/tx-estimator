<?php

use App\Models\Company;
use App\Models\ServiceTemplate;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Database\Seeders\ServiceTemplateSeeder;
use Inertia\Testing\AssertableInertia as Assert;

function seededCompany(string $slug): Company
{
    return Company::where('slug', $slug)->firstOrFail();
}

function templatePayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'Coil swap',
        'service_type' => 'HVAC repair',
        'scope' => 'Swap the coil.',
        'terms' => 'Net 30.',
        'lines' => [['name' => 'Labor', 'qty' => 1, 'unit' => 'job', 'labor' => 100, 'materials' => 20, 'fixed' => null]],
    ], $overrides);
}

beforeEach(function () {
    $this->seed([CompanySeeder::class, ServiceTemplateSeeder::class]);
    $this->air = seededCompany('crystal-creek-air');
    $this->pest = seededCompany('crystal-creek-pest-control');
});

test('seeder creates 14 templates in the bundle companies', function () {
    expect(ServiceTemplate::count())->toBe(14);
    expect(ServiceTemplate::where('company_id', $this->air->id)->count())->toBe(3)
        ->and(ServiceTemplate::where('company_id', $this->pest->id)->count())->toBe(5)
        ->and(ServiceTemplate::where('company_id', seededCompany('crystal-creek-roofing')->id)->count())->toBe(1)
        ->and(ServiceTemplate::where('company_id', seededCompany('texas-home-maintenance-pros')->id)->count())->toBe(5);

    expect(ServiceTemplate::where('key', 'm1')->first()->company_id)->toBe(seededCompany('texas-home-maintenance-pros')->id);

    $t1 = ServiceTemplate::where('key', 't1')->first();
    expect($t1->lines[0])->toMatchArray(['name' => 'Evaporator coil labor', 'unit' => 'job', 'fixed' => 1800]);

    $addOns = ServiceTemplate::where('key', 'p4')->first();
    expect($addOns->lines)->toHaveCount(8)
        ->and($addOns->lines[0]['qty'])->toEqual(0)
        ->and($addOns->lines[5]['unit'])->toBe('room');
});

test('estimator sees own company templates only', function () {
    $estimator = User::factory()->estimator($this->air)->create();

    $this->actingAs($estimator)->get(route('templates.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('templates/Index')
            ->has('templates', 3)
            ->where('canEdit', false));
});

test('admin in all companies sees every template', function () {
    $this->actingAs(User::factory()->admin()->create())->get(route('templates.index'))
        ->assertInertia(fn (Assert $page) => $page->has('templates', 14)->where('canEdit', true));
});

test('templates can be filtered by service', function () {
    $this->actingAs(User::factory()->admin()->create())->get(route('templates.index', ['service' => 'Pest plan']))
        ->assertInertia(fn (Assert $page) => $page->has('templates', 3));
});

test('templates can be filtered by catalog group', function () {
    $this->actingAs(User::factory()->admin()->create())->get(route('templates.index', ['group' => 'Maintenance']))
        ->assertInertia(fn (Assert $page) => $page->has('templates', 5));
});

test('estimator cannot update a template', function () {
    $estimator = User::factory()->estimator($this->air)->create();
    $template = ServiceTemplate::where('company_id', $this->air->id)->first();

    $this->actingAs($estimator)->put(route('templates.update', $template), templatePayload())->assertForbidden();
});

test('manager cannot edit another company template', function () {
    $manager = User::factory()->manager($this->air)->create();
    $template = ServiceTemplate::where('company_id', $this->pest->id)->first();

    $this->actingAs($manager)->get(route('templates.edit', $template))->assertForbidden();
    $this->actingAs($manager)->put(route('templates.update', $template), templatePayload())->assertForbidden();
});

test('manager updates rates', function () {
    $manager = User::factory()->manager($this->air)->create();
    $template = ServiceTemplate::where('company_id', $this->air->id)->first();

    $this->actingAs($manager)->put(route('templates.update', $template), templatePayload())
        ->assertRedirect(route('templates.index'));

    expect($template->fresh()->lines[0])->toMatchArray(['name' => 'Labor', 'labor' => 100, 'materials' => 20]);
});

test('manager creates a template in own company', function () {
    $manager = User::factory()->manager($this->air)->create();

    $this->actingAs($manager)->post(route('templates.store'), templatePayload(['company_id' => $this->pest->id]))
        ->assertRedirect(route('templates.index'));

    expect(ServiceTemplate::where('title', 'Coil swap')->first()->company_id)->toBe($this->air->id);
});

test('manager deletes a template', function () {
    $manager = User::factory()->manager($this->air)->create();
    $template = ServiceTemplate::where('company_id', $this->air->id)->first();

    $this->actingAs($manager)->delete(route('templates.destroy', $template))->assertRedirect(route('templates.index'));
    expect($template->fresh())->toBeNull();
});

test('invalid line is rejected', function () {
    $manager = User::factory()->manager($this->air)->create();
    $template = ServiceTemplate::where('company_id', $this->air->id)->first();

    $payload = templatePayload(['lines' => [['name' => 'Labor', 'qty' => -1, 'unit' => 'job', 'labor' => 0, 'materials' => 0, 'fixed' => null]]]);

    $this->actingAs($manager)->put(route('templates.update', $template), $payload)->assertSessionHasErrors('lines.0.qty');
});

test('service type must be from the list', function () {
    $manager = User::factory()->manager($this->air)->create();

    $this->actingAs($manager)->post(route('templates.store'), templatePayload(['service_type' => 'Gardening']))
        ->assertSessionHasErrors('service_type');
});
