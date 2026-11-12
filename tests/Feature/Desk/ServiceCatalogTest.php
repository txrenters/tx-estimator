<?php

use App\Models\Company;
use App\Models\Estimate;
use App\Models\ServiceGroup;
use App\Models\ServiceTemplate;
use App\Models\ServiceType;
use App\Models\User;
use App\Support\ServiceCatalog;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

function catalog(): ServiceCatalog
{
    return app(ServiceCatalog::class)->refresh();
}

test('the original ten services are seeded in four trades', function () {
    expect(catalog()->active())->toBe([
        'HVAC repair', 'HVAC replacement', 'Pest plan', 'Pest treatment', 'Roofing',
        'Siding', 'Make-ready', 'Cleaning', 'Plumbing', 'General maintenance',
    ]);
    expect(array_keys(catalog()->groups()))->toBe(['HVAC', 'Pest Control', 'Roofing', 'Maintenance']);
    expect(ServiceGroup::where('name', 'HVAC')->value('icon'))->toBe('wind');
});

test('admin adds a new trade with services and a company can use it', function () {
    $this->actingAs($this->admin)
        ->post(route('service-groups.store'), ['name' => 'Electrical', 'icon' => 'zap', 'color' => '#b7791f'])
        ->assertRedirect();
    $group = ServiceGroup::where('name', 'Electrical')->sole();

    $this->actingAs($this->admin)->post(route('services.store'), ['name' => 'Panel upgrade', 'service_group_id' => $group->id])->assertRedirect();
    $this->actingAs($this->admin)->post(route('services.store'), ['name' => 'Rewiring', 'service_group_id' => $group->id])->assertRedirect();

    expect(catalog()->groups()['Electrical'])->toBe(['Panel upgrade', 'Rewiring']);

    $company = Company::factory()->create(['service_types' => ['Panel upgrade', 'Rewiring']]);
    $estimator = User::factory()->estimator($company)->create();

    $this->actingAs($estimator)->post(route('estimates.store'), [
        'title' => 'Panel swap', 'service_type' => 'Panel upgrade',
        'lines' => [['name' => 'Labor', 'qty' => 1, 'unit' => 'job', 'labor' => 500, 'materials' => 0, 'fixed' => null]],
        'pricing' => config('estimating.pricing_defaults'),
    ])->assertRedirect();

    expect(Estimate::sole()->service_type)->toBe('Panel upgrade');
});

test('the services page lists trades for admins', function () {
    $this->actingAs($this->admin)->get(route('services.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('services/Index')
            ->has('groups', 4)
            ->where('groups.0.name', 'HVAC')
            ->has('groups.0.services', 2)
            ->where('groups.0.services.0.usage', 0));
});

test('managers and estimators cannot manage services', function () {
    $company = Company::factory()->create();
    $group = ServiceGroup::first();

    foreach ([User::factory()->manager($company)->create(), User::factory()->estimator($company)->create()] as $user) {
        $this->actingAs($user)->get(route('services.index'))->assertForbidden();
        $this->actingAs($user)->post(route('services.store'), ['name' => 'X', 'service_group_id' => $group->id])->assertForbidden();
    }
});

test('service names must be unique', function () {
    $this->actingAs($this->admin)
        ->post(route('services.store'), ['name' => 'Roofing', 'service_group_id' => ServiceGroup::first()->id])
        ->assertSessionHasErrors('name');
});

test('renaming a service updates templates, estimates and company lists', function () {
    $company = Company::factory()->create(['service_types' => ['Plumbing']]);
    $template = ServiceTemplate::factory()->for($company)->create(['service_type' => 'Plumbing']);
    $estimate = Estimate::factory()->for($company)->create(['service_type' => 'Plumbing']);
    $service = ServiceType::where('name', 'Plumbing')->sole();

    $this->actingAs($this->admin)->put(route('services.update', $service), [
        'name' => 'Plumbing repair', 'service_group_id' => $service->service_group_id, 'is_active' => true,
    ])->assertRedirect();

    expect($template->fresh()->service_type)->toBe('Plumbing repair')
        ->and($estimate->fresh()->service_type)->toBe('Plumbing repair')
        ->and($company->fresh()->serviceTypes())->toBe(['Plumbing repair']);
});

test('a retired service is no longer offered but old estimates keep it', function () {
    $company = Company::factory()->create(['service_types' => ['Cleaning', 'Plumbing']]);
    $estimate = Estimate::factory()->for($company)->create(['service_type' => 'Cleaning']);
    $service = ServiceType::where('name', 'Cleaning')->sole();
    $manager = User::factory()->manager($company)->create();

    $this->actingAs($this->admin)->put(route('services.update', $service), [
        'name' => 'Cleaning', 'service_group_id' => $service->service_group_id, 'is_active' => false,
    ])->assertRedirect();

    expect(catalog()->active())->not->toContain('Cleaning')
        ->and($company->fresh()->serviceTypes())->toBe(['Plumbing']);

    $payload = fn (string $service) => [
        'title' => 'Job', 'service_type' => $service,
        'lines' => [['name' => 'Labor', 'qty' => 1, 'unit' => 'job', 'labor' => 50, 'materials' => 0, 'fixed' => null]],
        'pricing' => config('estimating.pricing_defaults'),
    ];

    // New work cannot pick it…
    $this->actingAs($manager)->post(route('estimates.store'), $payload('Cleaning'))->assertSessionHasErrors('service_type');
    // …but an existing estimate that already uses it can still be saved.
    $this->actingAs($manager)->put(route('estimates.update', $estimate), $payload('Cleaning'))->assertSessionHasNoErrors();
});

test('an unused service can be deleted but a used one cannot', function () {
    $used = ServiceType::where('name', 'Siding')->sole();
    Estimate::factory()->create(['service_type' => 'Siding']);
    $unused = ServiceType::where('name', 'Make-ready')->sole();

    $this->actingAs($this->admin)->delete(route('services.destroy', $used))->assertSessionHasErrors('service');
    $this->actingAs($this->admin)->delete(route('services.destroy', $unused))->assertRedirect();

    expect(ServiceType::where('name', 'Siding')->exists())->toBeTrue()
        ->and(ServiceType::where('name', 'Make-ready')->exists())->toBeFalse();
});

test('admin edits a trade name, icon and colour', function () {
    $group = ServiceGroup::where('name', 'Maintenance')->sole();

    $this->actingAs($this->admin)->put(route('service-groups.update', $group), ['name' => 'Home maintenance', 'icon' => 'hammer', 'color' => '#62579b'])
        ->assertRedirect();

    expect(array_keys(catalog()->groups()))->toContain('Home maintenance');
});

test('a company without a logo takes the icon of its first trade', function () {
    $group = ServiceGroup::create(['name' => 'Electrical', 'icon' => 'zap', 'color' => '#b7791f', 'sort' => 9]);
    ServiceType::create(['name' => 'Panel upgrade', 'service_group_id' => $group->id, 'sort' => 99]);
    catalog();

    $company = Company::factory()->create(['service_types' => ['Panel upgrade']]);

    expect($company->summary()['icon'])->toBe('zap')
        ->and(Company::factory()->create(['service_types' => ['Roofing']])->summary()['icon'])->toBe('house');
});
