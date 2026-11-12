<?php

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;
use App\Notifications\InviteUser;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Notification::fake();
    $this->air = Company::factory()->create();
    $this->roof = Company::factory()->create();
});

function newUserPayload(array $overrides = []): array
{
    return array_merge(['name' => 'New Person', 'email' => 'new@example.com', 'role' => 'estimator', 'company_id' => null], $overrides);
}

test('manager sees only own company users', function () {
    $manager = User::factory()->manager($this->air)->create();
    User::factory()->estimator($this->air)->create();
    User::factory()->estimator($this->roof)->create();
    User::factory()->admin()->create();

    $this->actingAs($manager)->get(route('users.index'))
        ->assertInertia(fn (Assert $page) => $page->component('users/Index')->has('users', 2)->where('roles', ['manager', 'estimator']));
});

test('estimator cannot manage users', function () {
    $this->actingAs(User::factory()->estimator($this->air)->create())->get(route('users.index'))->assertForbidden();
});

test('manager creates an estimator in own company and a reset link is sent', function () {
    $manager = User::factory()->manager($this->air)->create();

    $this->actingAs($manager)->post(route('users.store'), newUserPayload(['company_id' => $this->roof->id]))->assertRedirect();

    $user = User::where('email', 'new@example.com')->sole();
    expect($user->company_id)->toBe($this->air->id)
        ->and($user->role)->toBe(Role::Estimator)
        ->and($user->email_verified_at)->not->toBeNull();
    Notification::assertSentTo($user, InviteUser::class);
});

test('manager cannot create an admin', function () {
    $manager = User::factory()->manager($this->air)->create();

    $this->actingAs($manager)->post(route('users.store'), newUserPayload(['role' => 'admin']))->assertSessionHasErrors('role');
});

test('admin creates manager but company is required', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('users.store'), newUserPayload(['role' => 'manager']))->assertSessionHasErrors('company_id');
    $this->actingAs($admin)->post(route('users.store'), newUserPayload(['role' => 'manager', 'company_id' => $this->roof->id]))->assertRedirect();

    expect(User::where('email', 'new@example.com')->sole()->company_id)->toBe($this->roof->id);
});

test('admin cannot create admins but superadmin can and admin roles have no company', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('users.store'), newUserPayload(['role' => 'admin']))->assertSessionHasErrors('role');

    $this->actingAs(User::factory()->superadmin()->create())
        ->post(route('users.store'), newUserPayload(['role' => 'admin', 'company_id' => $this->air->id]))->assertRedirect();

    expect(User::where('email', 'new@example.com')->sole()->company_id)->toBeNull();
});

test('admin cannot edit a superadmin', function () {
    $target = User::factory()->superadmin()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('users.update', $target), newUserPayload(['email' => $target->email, 'role' => 'superadmin']))
        ->assertForbidden();
});

test('manager cannot edit a user from another company', function () {
    $target = User::factory()->estimator($this->roof)->create();

    $this->actingAs(User::factory()->manager($this->air)->create())
        ->put(route('users.update', $target), newUserPayload(['email' => $target->email]))
        ->assertForbidden();
});

test('manager promotes an estimator to manager', function () {
    $target = User::factory()->estimator($this->air)->create();

    $this->actingAs(User::factory()->manager($this->air)->create())
        ->put(route('users.update', $target), ['name' => 'Promoted', 'email' => $target->email, 'role' => 'manager'])
        ->assertRedirect();

    expect($target->fresh()->role)->toBe(Role::Manager)->and($target->fresh()->company_id)->toBe($this->air->id);
});

test('users cannot deactivate themselves', function () {
    $manager = User::factory()->manager($this->air)->create();

    $this->actingAs($manager)->patch(route('users.active', $manager))->assertForbidden();
});

test('manager deactivates and reactivates an estimator', function () {
    $target = User::factory()->estimator($this->air)->create();
    $manager = User::factory()->manager($this->air)->create();

    $this->actingAs($manager)->patch(route('users.active', $target))->assertRedirect();
    expect($target->fresh()->is_active)->toBeFalse();

    $this->actingAs($manager)->patch(route('users.active', $target))->assertRedirect();
    expect($target->fresh()->is_active)->toBeTrue();
});

test('the last active superadmin cannot demote themselves', function () {
    $only = User::factory()->superadmin()->create();

    $this->actingAs($only)
        ->put(route('users.update', $only), ['name' => 'X', 'email' => $only->email, 'role' => 'admin'])
        ->assertSessionHasErrors('role');

    expect($only->fresh()->role)->toBe(Role::Superadmin);
});

test('a superadmin may step down when another superadmin remains', function () {
    $me = User::factory()->superadmin()->create();
    User::factory()->superadmin()->create();

    $this->actingAs($me)
        ->put(route('users.update', $me), ['name' => 'X', 'email' => $me->email, 'role' => 'admin'])
        ->assertRedirect();

    expect($me->fresh()->role)->toBe(Role::Admin);
});

test('the invitation email reads as an invitation with a set password link', function () {
    $manager = User::factory()->manager($this->air)->create();
    $this->actingAs($manager)->post(route('users.store'), newUserPayload());
    $user = User::where('email', 'new@example.com')->sole();

    Notification::assertSentTo($user, InviteUser::class, function (InviteUser $notification) use ($user) {
        $mail = $notification->toMail($user);

        return str_contains($mail->subject, 'invited')
            && str_contains($mail->actionUrl, '/reset-password/')
            && $mail->actionText === 'Set your password';
    });
});

test('manager can resend an invitation', function () {
    $target = User::factory()->estimator($this->air)->create();

    $this->actingAs(User::factory()->manager($this->air)->create())
        ->post(route('users.invite', $target))
        ->assertRedirect();

    Notification::assertSentTo($target, InviteUser::class);
});

test('manager cannot resend an invitation to another company user', function () {
    $target = User::factory()->estimator($this->roof)->create();

    $this->actingAs(User::factory()->manager($this->air)->create())
        ->post(route('users.invite', $target))
        ->assertForbidden();

    Notification::assertNothingSent();
});

test('a throttled resend reports that nothing was sent', function () {
    $target = User::factory()->estimator($this->air)->create();
    $manager = User::factory()->manager($this->air)->create();

    $this->actingAs($manager)->post(route('users.invite', $target));
    $this->actingAs($manager)->post(route('users.invite', $target))
        ->assertSessionHasErrors('invite');

    Notification::assertSentToTimes($target, InviteUser::class, 1);
});
