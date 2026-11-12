<?php

use App\Models\Company;
use App\Models\Estimate;
use App\Models\ServiceTemplate;
use App\Models\User;
use App\Support\ServiceCatalog;
use Database\Seeders\CompanySeeder;
use Database\Seeders\ServiceTemplateSeeder;
use Inertia\Testing\AssertableInertia as Assert;

function companyBySlug(string $slug): Company
{
    return Company::where('slug', $slug)->firstOrFail();
}

function linePayload(): array
{
    return [['name' => 'Labor', 'qty' => 1, 'unit' => 'job', 'labor' => 100, 'materials' => 0, 'fixed' => null]];
}

beforeEach(function () {
    $this->seed([CompanySeeder::class, ServiceTemplateSeeder::class]);
    $this->air = companyBySlug('crystal-creek-air');
});

test('each seeded company offers only its own trade', function () {
    expect($this->air->serviceTypes())->toBe(['HVAC repair', 'HVAC replacement'])
        ->and(companyBySlug('crystal-creek-pest-control')->serviceTypes())->toBe(['Pest plan', 'Pest treatment'])
        ->and(companyBySlug('crystal-creek-roofing')->serviceTypes())->toBe(['Roofing'])
        ->and(companyBySlug('texas-home-maintenance-pros')->serviceTypes())
        ->toBe(['Siding', 'Make-ready', 'Cleaning', 'Plumbing', 'General maintenance']);
});

test('a company without a list offers every service', function () {
    expect(Company::factory()->create()->serviceTypes())->toBe(app(ServiceCatalog::class)->active());
});

test('every seeded template fits its company trade', function () {
    ServiceTemplate::with('company')->get()->each(
        fn (ServiceTemplate $t) => expect($t->company->serviceTypes())->toContain($t->service_type)
    );
});

test('company services are shared with the page', function () {
    $this->actingAs(User::factory()->estimator($this->air)->create())->get(route('estimates.index'))
        ->assertInertia(fn (Assert $page) => $page->where('desk.currentCompany.services', ['HVAC repair', 'HVAC replacement']));
});

test('an estimate cannot use a service outside its company trade', function () {
    $estimator = User::factory()->estimator($this->air)->create();

    $this->actingAs($estimator)->post(route('estimates.store'), [
        'title' => 'Roof job', 'service_type' => 'Roofing', 'lines' => linePayload(),
        'pricing' => config('estimating.pricing_defaults'),
    ])->assertSessionHasErrors('service_type');

    expect(Estimate::count())->toBe(0);
});

test('updating an estimate checks its company trade too', function () {
    $estimate = Estimate::factory()->for($this->air)->create();

    $this->actingAs(User::factory()->manager($this->air)->create())->put(route('estimates.update', $estimate), [
        'title' => 'Plumbing job', 'service_type' => 'Plumbing', 'lines' => linePayload(),
        'pricing' => config('estimating.pricing_defaults'),
    ])->assertSessionHasErrors('service_type');
});

test('a template cannot use a service outside its company trade', function () {
    $this->actingAs(User::factory()->manager($this->air)->create())->post(route('templates.store'), [
        'title' => 'Ant job', 'service_type' => 'Pest treatment', 'lines' => linePayload(),
    ])->assertSessionHasErrors('service_type');
});

test('manager changes the company services in settings', function () {
    $roofing = companyBySlug('crystal-creek-roofing');

    $this->actingAs(User::factory()->manager($roofing)->create())->post(route('company-settings.update'), [
        'name' => $roofing->name, 'short_name' => $roofing->short_name, 'location' => $roofing->location,
        'color' => $roofing->color, 'services' => ['Roofing', 'Siding'],
    ])->assertRedirect();

    expect($roofing->fresh()->serviceTypes())->toBe(['Roofing', 'Siding']);
});

test('a company needs at least one valid service', function () {
    $roofing = companyBySlug('crystal-creek-roofing');
    $manager = User::factory()->manager($roofing)->create();
    $base = ['name' => $roofing->name, 'short_name' => $roofing->short_name, 'location' => $roofing->location, 'color' => $roofing->color];

    $this->actingAs($manager)->post(route('company-settings.update'), [...$base, 'services' => []])
        ->assertSessionHasErrors('services');
    $this->actingAs($manager)->post(route('company-settings.update'), [...$base, 'services' => ['Gardening']])
        ->assertSessionHasErrors('services.0');
});

test('company settings groups the service checklist by trade', function () {
    $roofing = companyBySlug('crystal-creek-roofing');

    $this->actingAs(User::factory()->manager($roofing)->create())->get(route('company-settings.edit'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('company/Settings')
            ->has('serviceGroups', 4)
            ->where('serviceGroups.2.name', 'Roofing')
            ->where('serviceGroups.2.icon', 'house')
            ->where('serviceGroups.2.services', ['Roofing'])
            ->where('company.services', ['Roofing']));
});

test('the services catalog shows which companies offer each service', function () {
    $this->actingAs(User::factory()->admin()->create())->get(route('services.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('groups.0.name', 'HVAC')
            ->where('groups.0.services.0.offeredBy', ['Air'])
            ->where('groups.3.services.0.offeredBy', ['Maintenance']));
});

test('a company that offers everything is listed under every service', function () {
    Company::factory()->create(['short_name' => 'Allround', 'service_types' => null]);

    $this->actingAs(User::factory()->admin()->create())->get(route('services.index'))
        ->assertInertia(fn (Assert $page) => $page->where('groups.0.services.0.offeredBy', ['Air', 'Allround']));
});
