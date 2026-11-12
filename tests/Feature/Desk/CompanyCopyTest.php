<?php

use App\Models\Company;
use App\Models\ServiceTemplate;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Database\Seeders\ServiceTemplateSeeder;

beforeEach(function () {
    $this->seed([CompanySeeder::class, ServiceTemplateSeeder::class]);
    $this->admin = User::factory()->admin()->create();
    $this->thmp = Company::where('slug', 'texas-home-maintenance-pros')->sole();
    $this->thmp->update([
        'pricing_defaults' => [...config('estimating.pricing_defaults'), 'markup' => 42],
        'default_terms' => 'THMP terms.',
    ]);
});

function newCompany(array $overrides = []): array
{
    return array_merge(['name' => 'Crystal Creek Plumbing', 'short_name' => 'Plumbing', 'location' => 'Katy, TX', 'color' => '#336699'], $overrides);
}

test('a blank company offers every service, default pricing and no templates', function () {
    $this->actingAs($this->admin)->post(route('companies.store'), newCompany())->assertRedirect();

    $company = Company::where('name', 'Crystal Creek Plumbing')->sole();
    expect($company->service_types)->toBeNull()
        ->and($company->pricing_defaults)->toEqual(config('estimating.pricing_defaults'))
        ->and(ServiceTemplate::where('company_id', $company->id)->count())->toBe(0);
});

test('a company can start from another company services, pricing and templates', function () {
    $this->actingAs($this->admin)->post(route('companies.store'), newCompany([
        'copy_from' => $this->thmp->id,
        'copy' => ['services', 'pricing', 'templates'],
    ]))->assertRedirect();

    $company = Company::where('name', 'Crystal Creek Plumbing')->sole();
    expect($company->serviceTypes())->toBe($this->thmp->serviceTypes())
        ->and($company->pricing_defaults['markup'])->toEqual(42)
        ->and($company->default_terms)->toBe('THMP terms.');

    $copied = ServiceTemplate::where('company_id', $company->id)->orderBy('id')->get();
    expect($copied)->toHaveCount(5)
        ->and($copied->pluck('title')->all())->toBe(ServiceTemplate::where('company_id', $this->thmp->id)->orderBy('id')->pluck('title')->all())
        ->and($copied->pluck('key')->filter()->all())->toBe([]);
});

test('copying only templates keeps every service and default pricing', function () {
    $this->actingAs($this->admin)->post(route('companies.store'), newCompany([
        'copy_from' => $this->thmp->id,
        'copy' => ['templates'],
    ]))->assertRedirect();

    $company = Company::where('name', 'Crystal Creek Plumbing')->sole();
    expect($company->service_types)->toBeNull()
        ->and($company->pricing_defaults['markup'])->toEqual(30)
        ->and(ServiceTemplate::where('company_id', $company->id)->count())->toBe(5);
});

test('the source company must exist', function () {
    $this->actingAs($this->admin)->post(route('companies.store'), newCompany(['copy_from' => 999, 'copy' => ['templates']]))
        ->assertSessionHasErrors('copy_from');
});
