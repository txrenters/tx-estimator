<?php

use App\Models\Company;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guest visiting home is redirected to login', function () {
    $this->get('/')->assertRedirect(route('login'));
});

test('signed in user visiting home is redirected to estimates', function () {
    $user = User::factory()->estimator(Company::factory()->create())->create();

    $this->actingAs($user)->get('/')->assertRedirect(route('estimates.index'));
});

test('login redirects home, which routes by role', function () {
    $user = User::factory()->estimator(Company::factory()->create())->create();

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('home', absolute: false));
});

test('estimates page renders inside the desk', function () {
    $user = User::factory()->estimator(Company::factory()->create())->create();

    $this->actingAs($user)->get(route('estimates.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('estimates/Index'));
});
