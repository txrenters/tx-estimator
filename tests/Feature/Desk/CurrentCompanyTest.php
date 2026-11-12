<?php

use App\Models\Company;
use App\Models\User;
use App\Support\CurrentCompany;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->air = Company::factory()->create(['name' => 'Air']);
    $this->roof = Company::factory()->create(['name' => 'Roof']);
});

test('admin can switch company and back to all companies', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('current-company.update'), ['company_id' => $this->roof->id])
        ->assertRedirect();
    expect(session('current_company_id'))->toBe($this->roof->id);

    $this->actingAs($admin)->post(route('current-company.update'), ['company_id' => null])
        ->assertRedirect();
    expect(session('current_company_id'))->toBeNull();
});

test('admin cannot switch to a missing company', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('current-company.update'), ['company_id' => 999])
        ->assertSessionHasErrors('company_id');
});

test('manager cannot switch company', function () {
    $manager = User::factory()->manager($this->air)->create();

    $this->actingAs($manager)->post(route('current-company.update'), ['company_id' => $this->roof->id])
        ->assertForbidden();
});

test('pinned user current company is always their own even with a forged session value', function () {
    $estimator = User::factory()->estimator($this->air)->create();

    $this->actingAs($estimator)->withSession(['current_company_id' => $this->roof->id]);
    $this->get('/settings/profile');

    expect(app(CurrentCompany::class)->get()->id)->toBe($this->air->id)
        ->and(app(CurrentCompany::class)->visibleCompanyIds())->toBe([$this->air->id]);
});

test('company for write ignores requested id for pinned users', function () {
    $estimator = User::factory()->estimator($this->air)->create();
    $this->actingAs($estimator);

    expect(app(CurrentCompany::class)->companyForWrite($this->roof->id)->id)->toBe($this->air->id);
});

test('admin in all companies must request a company for write', function () {
    $this->actingAs(User::factory()->admin()->create());

    expect(fn () => app(CurrentCompany::class)->companyForWrite(null))
        ->toThrow(Illuminate\Validation\ValidationException::class);
    expect(app(CurrentCompany::class)->companyForWrite($this->roof->id)->id)->toBe($this->roof->id);
});

test('inactive user is logged out on next request', function () {
    $user = User::factory()->estimator($this->air)->inactive()->create();

    $this->actingAs($user)->get('/settings/profile')->assertRedirect(route('login'));
    $this->assertGuest();
});

test('inactive user cannot log in', function () {
    $user = User::factory()->estimator($this->air)->inactive()->create();

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('shared props list only own company for estimator', function () {
    $estimator = User::factory()->estimator($this->air)->create();

    $this->actingAs($estimator)->get('/settings/profile')
        ->assertInertia(fn (Assert $page) => $page
            ->has('desk.companies', 1)
            ->where('desk.currentCompany.id', $this->air->id)
            ->where('desk.user.role', 'estimator')
            ->where('desk.can.switchCompany', false)
            ->where('desk.can.approve', false)
        );
});

test('shared props list all companies for admin', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/settings/profile')
        ->assertInertia(fn (Assert $page) => $page
            ->has('desk.companies', 2)
            ->where('desk.currentCompany', null)
            ->where('desk.can.switchCompany', true)
            ->where('desk.can.viewOverview', true)
            ->has('desk.serviceTypes', 10)
            ->has('desk.statuses', 4)
        );
});
