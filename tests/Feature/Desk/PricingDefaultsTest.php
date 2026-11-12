<?php

use App\Models\Company;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

function defaultsPayload(array $overrides = []): array
{
    return array_merge(['markup' => 35, 'discount' => 5, 'trip' => 49, 'authorization' => 300, 'manager' => 1500, 'target' => 30], $overrides);
}

test('manager saves pricing defaults for own company', function () {
    $company = Company::factory()->create();
    $manager = User::factory()->manager($company)->create();

    $this->actingAs($manager)->put(route('pricing-defaults.update'), defaultsPayload())->assertRedirect();

    expect($company->fresh()->pricing_defaults)->toEqual(defaultsPayload());
});

test('pricing defaults page shows the company values', function () {
    $company = Company::factory()->create();

    $this->actingAs(User::factory()->estimator($company)->create())->get(route('pricing-defaults.edit'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('PricingDefaults')
            ->where('defaults.markup', 30)
            ->where('canEdit', false));
});

test('estimator cannot update pricing defaults', function () {
    $company = Company::factory()->create();

    $this->actingAs(User::factory()->estimator($company)->create())
        ->put(route('pricing-defaults.update'), defaultsPayload())->assertForbidden();
});

test('admin in all companies cannot save defaults without choosing a company', function () {
    Company::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('pricing-defaults.update'), defaultsPayload())->assertSessionHasErrors('company_id');
});

test('admin saves defaults for the selected company', function () {
    $company = Company::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->withSession(['current_company_id' => $company->id])
        ->put(route('pricing-defaults.update'), defaultsPayload(['markup' => 40]))->assertRedirect();

    expect($company->fresh()->pricing_defaults['markup'])->toEqual(40);
});

test('target margin cannot exceed 100', function () {
    $company = Company::factory()->create();

    $this->actingAs(User::factory()->manager($company)->create())
        ->put(route('pricing-defaults.update'), defaultsPayload(['target' => 120]))->assertSessionHasErrors('target');
});
