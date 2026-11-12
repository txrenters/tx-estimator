<?php

use App\Models\Company;
use App\Models\Estimate;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->air = Company::factory()->create(['logo_path' => 'logos/1/air.png', 'location' => 'Greater Houston, TX']);
    $this->estimate = Estimate::factory()->for($this->air)->create([
        'notes' => 'Secret vendor quote',
        'lines' => [
            ['name' => 'Labor', 'qty' => 2, 'unit' => 'hr', 'labor' => 100, 'materials' => 50, 'fixed' => null],
            ['name' => 'Unused add-on', 'qty' => 0, 'unit' => 'visit', 'labor' => 0, 'materials' => 0, 'fixed' => 75],
        ],
        'pricing' => [...config('estimating.pricing_defaults'), 'discount' => 10, 'trip' => 50],
    ]);
});

test('proposal shows sell prices and hides costs and notes', function () {
    $this->actingAs(User::factory()->estimator($this->air)->create())
        ->get(route('estimates.proposal', $this->estimate))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('estimates/Proposal')
            ->missing('proposal.notes')
            ->has('proposal.lines', 1)
            ->where('proposal.lines.0', ['name' => 'Labor', 'qty' => 2, 'unit' => 'hr', 'amount' => 390])
            ->where('proposal.discount', 39)
            ->where('proposal.trip', 50)
            ->where('proposal.total', 401)
        );
});

test('proposal includes the company logo url', function () {
    $this->actingAs(User::factory()->estimator($this->air)->create())
        ->get(route('estimates.proposal', $this->estimate))
        ->assertInertia(fn (Assert $page) => $page
            ->where('proposal.company.logoUrl', Storage::disk('public')->url('logos/1/air.png')));
});

test('another company proposal is forbidden', function () {
    $this->actingAs(User::factory()->manager(Company::factory()->create())->create())
        ->get(route('estimates.proposal', $this->estimate))->assertForbidden();
});
