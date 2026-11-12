<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Storage::fake('public');
    $this->air = Company::factory()->create(['name' => 'Air']);
    $this->manager = User::factory()->manager($this->air)->create();
});

function settingsPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Crystal Creek Air',
        'short_name' => 'Air',
        'location' => 'Katy, TX',
        'color' => '#287f9d',
        'default_scope' => 'Default scope.',
        'default_terms' => 'Default terms.',
    ], $overrides);
}

test('manager updates settings and uploads a logo that is shared in desk props', function () {
    $this->actingAs($this->manager)
        ->post(route('company-settings.update'), settingsPayload(['logo' => UploadedFile::fake()->image('logo.png', 200, 80)]))
        ->assertRedirect();

    $company = $this->air->fresh();
    expect($company->location)->toBe('Katy, TX')
        ->and($company->default_terms)->toBe('Default terms.')
        ->and($company->logo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($company->logo_path);

    $this->actingAs($this->manager)->get(route('company-settings.edit'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('company/Settings')
            ->where('desk.currentCompany.logoUrl', Storage::disk('public')->url($company->logo_path)));
});

test('replacing a logo deletes the old file', function () {
    $this->actingAs($this->manager)->post(route('company-settings.update'), settingsPayload(['logo' => UploadedFile::fake()->image('one.png')]));
    $old = $this->air->fresh()->logo_path;

    $this->actingAs($this->manager)->post(route('company-settings.update'), settingsPayload(['logo' => UploadedFile::fake()->image('two.png')]));

    Storage::disk('public')->assertMissing($old);
    Storage::disk('public')->assertExists($this->air->fresh()->logo_path);
});

test('removing the logo deletes the file', function () {
    $this->actingAs($this->manager)->post(route('company-settings.update'), settingsPayload(['logo' => UploadedFile::fake()->image('one.png')]));
    $old = $this->air->fresh()->logo_path;

    $this->actingAs($this->manager)->post(route('company-settings.update'), settingsPayload(['remove_logo' => true]));

    Storage::disk('public')->assertMissing($old);
    expect($this->air->fresh()->logo_path)->toBeNull();
});

test('a pdf is rejected as a logo', function () {
    $this->actingAs($this->manager)
        ->post(route('company-settings.update'), settingsPayload(['logo' => UploadedFile::fake()->create('logo.pdf', 10, 'application/pdf')]))
        ->assertSessionHasErrors('logo');
});

test('a logo over 2MB is rejected', function () {
    $this->actingAs($this->manager)
        ->post(route('company-settings.update'), settingsPayload(['logo' => UploadedFile::fake()->image('big.png')->size(3000)]))
        ->assertSessionHasErrors('logo');
});

test('color must be a hex value', function () {
    $this->actingAs($this->manager)
        ->post(route('company-settings.update'), settingsPayload(['color' => 'red']))
        ->assertSessionHasErrors('color');
});

test('estimator cannot open settings', function () {
    $this->actingAs(User::factory()->estimator($this->air)->create())->get(route('company-settings.edit'))->assertForbidden();
    $this->actingAs(User::factory()->estimator($this->air)->create())->post(route('company-settings.update'), settingsPayload())->assertForbidden();
});

test('admin must choose a company before editing settings', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('company-settings.edit'))->assertRedirect(route('companies.index'));
    $this->actingAs($admin)->withSession(['current_company_id' => $this->air->id])
        ->post(route('company-settings.update'), settingsPayload(['name' => 'Renamed']))->assertRedirect();

    expect($this->air->fresh()->name)->toBe('Renamed');
});

test('admin creates a company with default pricing', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('companies.store'), ['name' => 'Crystal Creek Plumbing', 'short_name' => 'Plumbing', 'location' => 'Greater Houston, TX', 'color' => '#336699'])
        ->assertRedirect();

    $company = Company::where('name', 'Crystal Creek Plumbing')->sole();
    expect($company->slug)->toBe('crystal-creek-plumbing')
        ->and($company->pricing_defaults)->toEqual(config('estimating.pricing_defaults'))
        ->and($company->tone)->toMatch('/^#[0-9a-f]{6}$/i');
});

test('companies list shows counts for admins only', function () {
    User::factory()->estimator($this->air)->create();

    $this->actingAs(User::factory()->admin()->create())->get(route('companies.index'))
        ->assertInertia(fn (Assert $page) => $page->component('companies/Index')->has('companies', 1)->where('companies.0.users', 2));

    $this->actingAs($this->manager)->get(route('companies.index'))->assertForbidden();
});

test('manager cannot create a company', function () {
    $this->actingAs($this->manager)
        ->post(route('companies.store'), ['name' => 'X Co', 'short_name' => 'X', 'location' => 'TX', 'color' => '#336699'])
        ->assertForbidden();
});
