<?php

use App\Enums\EstimateStatus;
use App\Models\Company;
use App\Models\Estimate;
use App\Models\EstimateRevision;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->air = Company::factory()->create();
    $this->manager = User::factory()->manager($this->air)->create();
});

function saveEstimate(User $user, ?Estimate $estimate, string $title)
{
    $payload = [
        'title' => $title, 'service_type' => 'HVAC repair',
        'lines' => [['name' => 'Labor', 'qty' => 1, 'unit' => 'job', 'labor' => 100, 'materials' => 0, 'fixed' => null]],
        'pricing' => config('estimating.pricing_defaults'),
    ];

    return $estimate
        ? test()->actingAs($user)->put(route('estimates.update', $estimate), $payload)
        : test()->actingAs($user)->post(route('estimates.store'), $payload);
}

test('each save records a revision with a snapshot', function () {
    saveEstimate($this->manager, null, 'First');
    $estimate = Estimate::sole();
    saveEstimate($this->manager, $estimate, 'Second');

    expect(EstimateRevision::count())->toBe(2);
    $latest = EstimateRevision::latest('id')->first();
    expect($latest->snapshot['title'])->toBe('Second')
        ->and($latest->snapshot['lines'][0]['labor'])->toEqual(100)
        ->and((float) $latest->snapshot['total'])->toBe(130.0)
        ->and($latest->user_id)->toBe($this->manager->id);
});

test('approve and decline record revisions', function () {
    $estimate = Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Pending]);

    $this->actingAs($this->manager)->post(route('estimates.status', $estimate), ['action' => 'approve']);

    expect(EstimateRevision::where('estimate_id', $estimate->id)->count())->toBe(1)
        ->and(EstimateRevision::sole()->snapshot['status'])->toBe('approved');
});

test('edit page exposes revisions newest first', function () {
    saveEstimate($this->manager, null, 'First');
    $estimate = Estimate::sole();
    saveEstimate($this->manager, $estimate, 'Second');

    $this->actingAs($this->manager)->get(route('estimates.edit', $estimate))
        ->assertInertia(fn (Assert $page) => $page
            ->has('revisions', 2)
            ->where('revisions.0.snapshot.title', 'Second')
            ->where('revisions.1.snapshot.title', 'First')
            ->where('revisions.0.user', $this->manager->name));
});

test('revisions of another company estimate are not exposed', function () {
    $other = Estimate::factory()->for(Company::factory()->create())->create();

    $this->actingAs($this->manager)->get(route('estimates.edit', $other))->assertForbidden();
});
