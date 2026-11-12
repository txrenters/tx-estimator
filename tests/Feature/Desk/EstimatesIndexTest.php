<?php

use App\Enums\EstimateStatus;
use App\Models\Company;
use App\Models\Estimate;
use App\Models\ServiceTemplate;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->air = Company::factory()->create(['name' => 'Air']);
    $this->roof = Company::factory()->create(['name' => 'Roof']);
});

test('estimator sees all estimates of own company and none of others', function () {
    $me = User::factory()->estimator($this->air)->create();
    Estimate::factory()->for($this->air)->for($me, 'creator')->create();
    Estimate::factory()->for($this->air)->create();
    Estimate::factory()->for($this->roof)->create();

    $this->actingAs($me)->get(route('estimates.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('estimates/Index')
            ->has('estimates.data', 2)
            ->where('stats.total', 2));
});

test('admin in all companies sees everything and can filter by company', function () {
    Estimate::factory()->for($this->air)->count(2)->create();
    Estimate::factory()->for($this->roof)->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('estimates.index'))
        ->assertInertia(fn (Assert $page) => $page->has('estimates.data', 3));

    $this->actingAs($admin)->get(route('estimates.index', ['company' => $this->roof->id]))
        ->assertInertia(fn (Assert $page) => $page->has('estimates.data', 1));
});

test('stats reflect the scope', function () {
    Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Approved, 'total' => 1000]);
    Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Approved, 'total' => 250.5]);
    Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Pending, 'total' => 999]);
    Estimate::factory()->for($this->roof)->create(['status' => EstimateStatus::Approved, 'total' => 5000]);
    ServiceTemplate::factory()->for($this->air)->count(2)->create();
    ServiceTemplate::factory()->for($this->roof)->create();

    $this->actingAs(User::factory()->manager($this->air)->create())->get(route('estimates.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('stats.total', 3)
            ->where('stats.pending', 1)
            ->where('stats.approvedValue', 1250.5)
            ->where('stats.templates', 2));
});

test('filters by status and service', function () {
    Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Pending, 'service_type' => 'HVAC repair']);
    Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Draft, 'service_type' => 'HVAC repair']);
    Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Pending, 'service_type' => 'Roofing']);
    $manager = User::factory()->manager($this->air)->create();

    $this->actingAs($manager)->get(route('estimates.index', ['status' => 'pending']))
        ->assertInertia(fn (Assert $page) => $page->has('estimates.data', 2));
    $this->actingAs($manager)->get(route('estimates.index', ['status' => 'pending', 'service' => 'Roofing']))
        ->assertInertia(fn (Assert $page) => $page->has('estimates.data', 1));
});

test('estimate numbers look like the original', function () {
    $estimate = Estimate::factory()->create();

    expect($estimate->number)->toMatch('/^EST-[0-9A-F]{6}$/');
});

test('search matches title, number and customer', function () {
    Estimate::factory()->for($this->air)->create(['title' => 'Coil swap', 'client' => 'Jane Roe']);
    $other = Estimate::factory()->for($this->air)->create(['title' => 'Roof patch', 'client' => 'Bob Lee']);
    $manager = User::factory()->manager($this->air)->create();

    $this->actingAs($manager)->get(route('estimates.index', ['q' => 'coil']))
        ->assertInertia(fn (Assert $page) => $page->has('estimates.data', 1));
    $this->actingAs($manager)->get(route('estimates.index', ['q' => 'bob']))
        ->assertInertia(fn (Assert $page) => $page->has('estimates.data', 1));
    $this->actingAs($manager)->get(route('estimates.index', ['q' => $other->number]))
        ->assertInertia(fn (Assert $page) => $page->has('estimates.data', 1)->where('estimates.data.0.id', $other->id));
});

test('the estimates page no longer carries the template catalog', function () {
    $this->actingAs(User::factory()->manager($this->air)->create())->get(route('estimates.index'))
        ->assertInertia(fn (Assert $page) => $page->component('estimates/Index')->missing('catalog')->has('stats.templates'));
});
