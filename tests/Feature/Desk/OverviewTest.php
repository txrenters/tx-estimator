<?php

use App\Enums\EstimateStatus;
use App\Models\Company;
use App\Models\Estimate;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->air = Company::factory()->create(['name' => 'Air']);
    $this->roof = Company::factory()->create(['name' => 'Roof']);
    $this->admin = User::factory()->admin()->create();
});

test('managers and estimators cannot view the overview', function () {
    $this->actingAs(User::factory()->manager($this->air)->create())->get(route('overview'))->assertForbidden();
    $this->actingAs(User::factory()->estimator($this->air)->create())->get(route('overview'))->assertForbidden();
});

function seedOverviewEstimates(Company $air, Company $roof): void
{
    Estimate::factory()->for($air)->create(['status' => EstimateStatus::Approved, 'total' => 1000, 'margin' => 30]);
    Estimate::factory()->for($roof)->create(['status' => EstimateStatus::Approved, 'total' => 500, 'margin' => 20]);
    Estimate::factory()->for($roof)->create(['status' => EstimateStatus::Declined, 'total' => 50, 'margin' => null]);
    Estimate::factory()->for($roof)->create(['status' => EstimateStatus::Pending, 'total' => 75, 'margin' => null]);
}

test('with all companies selected the totals span every company', function () {
    seedOverviewEstimates($this->air, $this->roof);

    $this->actingAs($this->admin)->get(route('overview'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Overview')
            ->where('scope', null)
            ->where('tiles.total', 4)
            ->where('tiles.pending', 1)
            ->where('tiles.declined', 1)
            ->where('tiles.approvedValue', 1500)
            ->where('tiles.averageMargin', 25)
            ->has('companies', 2)
            ->where('companies.1.name', 'Roof')
            ->where('companies.1.estimates', 3)
            ->where('companies.1.approvedValue', 500));
});

test('switching company scopes the overview to that company', function () {
    seedOverviewEstimates($this->air, $this->roof);
    $pending = Estimate::where('status', EstimateStatus::Pending)->sole();
    $this->actingAs(User::factory()->manager($this->roof)->create())
        ->post(route('estimates.status', $pending), ['action' => 'decline']);

    $this->actingAs($this->admin)->withSession(['current_company_id' => $this->air->id])->get(route('overview'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('scope.id', $this->air->id)
            ->where('tiles.total', 1)
            ->where('tiles.pending', 0)
            ->where('tiles.declined', 0)
            ->where('tiles.approvedValue', 1000)
            ->where('tiles.averageMargin', 30)
            ->has('companies', 1)
            ->where('companies.0.name', 'Air')
            ->has('pendingQueue', 0)
            ->has('activity', 0));
});

test('pending queue lists pending estimates oldest first', function () {
    $newer = Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Pending, 'updated_at' => now()]);
    $older = Estimate::factory()->for($this->roof)->create(['status' => EstimateStatus::Pending, 'updated_at' => now()->subDay()]);
    Estimate::factory()->for($this->roof)->create(['status' => EstimateStatus::Draft]);

    $this->actingAs($this->admin)->get(route('overview'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('pendingQueue', 2)
            ->where('pendingQueue.0.id', $older->id)
            ->where('pendingQueue.1.id', $newer->id));
});

test('activity shows the latest transitions', function () {
    $estimator = User::factory()->estimator($this->air)->create();
    $estimate = Estimate::factory()->for($this->air)->for($estimator, 'creator')->create();
    $this->actingAs($estimator)->post(route('estimates.status', $estimate), ['action' => 'submit']);

    $this->actingAs($this->admin)->get(route('overview'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('activity', 1)
            ->where('activity.0.action', 'submitted')
            ->where('activity.0.user', $estimator->name)
            ->where('activity.0.number', $estimate->number));
});

test('admin login lands on overview and manager on estimates', function () {
    $this->post(route('login.store'), ['email' => $this->admin->email, 'password' => 'password']);
    $this->get('/')->assertRedirect(route('overview'));

    auth()->logout();

    $manager = User::factory()->manager($this->air)->create();
    $this->post(route('login.store'), ['email' => $manager->email, 'password' => 'password']);
    $this->get('/')->assertRedirect(route('estimates.index'));
});
