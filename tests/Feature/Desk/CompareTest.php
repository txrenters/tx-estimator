<?php

use App\Models\Company;
use App\Models\Estimate;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->air = Company::factory()->create();
    $this->manager = User::factory()->manager($this->air)->create();
});

test('compare lists visible estimates as options', function () {
    Estimate::factory()->for($this->air)->count(2)->create();
    Estimate::factory()->for(Company::factory()->create())->create();

    $this->actingAs($this->manager)->get(route('compare.index'))
        ->assertInertia(fn (Assert $page) => $page->component('Compare')->has('options', 2)->has('selected', 0));
});

test('compare caps at three', function () {
    $ids = Estimate::factory()->for($this->air)->count(4)->create()->pluck('id')->all();

    $this->actingAs($this->manager)->get(route('compare.index', ['ids' => $ids]))
        ->assertInertia(fn (Assert $page) => $page->has('selected', 3));
});

test('compare drops another company estimate ids', function () {
    $mine = Estimate::factory()->for($this->air)->create();
    $theirs = Estimate::factory()->for(Company::factory()->create())->create();

    $this->actingAs($this->manager)->get(route('compare.index', ['ids' => [$mine->id, $theirs->id]]))
        ->assertInertia(fn (Assert $page) => $page->has('selected', 1)->where('selected.0.id', $mine->id));
});

test('compare rows carry pricing figures', function () {
    $estimate = Estimate::factory()->for($this->air)->create([
        'lines' => [['name' => 'Labor', 'qty' => 2, 'unit' => 'hr', 'labor' => 100, 'materials' => 50, 'fixed' => null]],
    ]);

    $this->actingAs($this->manager)->get(route('compare.index', ['ids' => [$estimate->id]]))
        ->assertInertia(fn (Assert $page) => $page
            ->where('selected.0.cost', 300)
            ->where('selected.0.total', 390)
            ->where('selected.0.markup', 30));
});

test('calculator page renders', function () {
    $this->actingAs($this->manager)->get(route('calculator'))
        ->assertOk()->assertInertia(fn (Assert $page) => $page->component('Calculator'));
});
