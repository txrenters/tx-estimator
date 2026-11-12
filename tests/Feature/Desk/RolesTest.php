<?php

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Illuminate\Database\QueryException;

test('seeder creates the four companies with original colors', function () {
    $this->seed(CompanySeeder::class);

    expect(Company::count())->toBe(4);

    $air = Company::where('name', 'Crystal Creek Air')->firstOrFail();
    expect($air->color)->toBe('#287f9d')
        ->and($air->tone)->toBe('#e9f4f8')
        ->and($air->short_name)->toBe('Air')
        ->and($air->location)->toBe('Greater Houston, TX')
        ->and($air->currency)->toBe('USD')
        ->and($air->pricing_defaults)->toEqual(config('estimating.pricing_defaults'));

    expect(Company::orderBy('id')->pluck('name')->all())->toBe([
        'Crystal Creek Air',
        'Crystal Creek Pest Control',
        'Crystal Creek Roofing',
        'Texas Home Maintenance Pros',
    ]);
});

test('pricing defaults match the original', function () {
    expect(config('estimating.pricing_defaults'))->toBe([
        'markup' => 30, 'discount' => 0, 'trip' => 0,
        'authorization' => 250, 'manager' => 1000, 'target' => 25,
    ]);
});

test('a company with users cannot be deleted', function () {
    $company = Company::factory()->create();
    User::factory()->manager($company)->create();

    expect(fn () => $company->delete())->toThrow(QueryException::class);
});

test('global roles and approval rights', function () {
    $company = Company::factory()->create();

    expect(User::factory()->superadmin()->make()->isGlobal())->toBeTrue()
        ->and(User::factory()->admin()->make()->isGlobal())->toBeTrue()
        ->and(User::factory()->manager($company)->make()->isGlobal())->toBeFalse()
        ->and(User::factory()->estimator($company)->make()->isGlobal())->toBeFalse();

    expect(User::factory()->estimator($company)->make()->canApprove())->toBeFalse()
        ->and(User::factory()->manager($company)->make()->canApprove())->toBeTrue()
        ->and(User::factory()->admin()->make()->canApprove())->toBeTrue();

    expect(User::factory()->manager($company)->make()->role)->toBe(Role::Manager);
});
