<?php

use App\Models\Company;
use App\Models\Estimate;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->air = Company::factory()->create();
    $this->manager = User::factory()->manager($this->air)->create();
    $this->estimate = Estimate::factory()->for($this->air)->create();
    $this->actingAs($this->manager)->post(route('estimates.share', $this->estimate));
    $this->token = $this->estimate->fresh()->share_token;
    actAsCustomer();
});

function authorizeAs(string $token, array $data = [])
{
    $estimate = Estimate::where('share_token', $token)->first();
    $url = $estimate ? $estimate->shareUrl() : url('/p/x/'.$token);

    return test()->post($url.'/authorize', array_merge(['name' => 'Jane Roe', 'agree' => true], $data));
}

test('the property owner authorizes the proposal by typing their name', function () {
    authorizeAs($this->token)->assertRedirect($this->estimate->fresh()->shareUrl());

    $fresh = $this->estimate->fresh();
    expect($fresh->authorized_name)->toBe('Jane Roe')
        ->and($fresh->authorized_at)->not->toBeNull();

    $this->get($this->estimate->fresh()->shareUrl())
        ->assertInertia(fn (Assert $page) => $page
            ->where('authorization.name', 'Jane Roe')
            ->has('authorization.at'));
});

test('a name and agreement are required', function () {
    authorizeAs($this->token, ['name' => ''])->assertSessionHasErrors('name');
    authorizeAs($this->token, ['name' => 'J'])->assertSessionHasErrors('name');
    authorizeAs($this->token, ['agree' => false])->assertSessionHasErrors('agree');

    expect($this->estimate->fresh()->authorized_at)->toBeNull();
});

test('a proposal can only be authorized once', function () {
    authorizeAs($this->token);
    authorizeAs($this->token, ['name' => 'Someone Else'])->assertSessionHasErrors('name');

    expect($this->estimate->fresh()->authorized_name)->toBe('Jane Roe');
});

test('a turned off link cannot be authorized', function () {
    $this->actingAs($this->manager)->delete(route('estimates.unshare', $this->estimate));
    actAsCustomer();

    authorizeAs($this->token)->assertNotFound();
});

test('the authorization is logged and shown on the estimate', function () {
    authorizeAs($this->token);

    $activity = $this->estimate->activities()->latest('id')->first();
    expect($activity->action)->toBe('customer_authorized')
        ->and($activity->user_id)->toBeNull()
        ->and($activity->actor_name)->toBe('Jane Roe');

    $this->actingAs($this->manager)->get(route('estimates.edit', $this->estimate))
        ->assertInertia(fn (Assert $page) => $page->where('estimate.authorization.name', 'Jane Roe'));

    $this->actingAs($this->manager)->get(route('estimates.proposal', $this->estimate))
        ->assertInertia(fn (Assert $page) => $page->where('authorization.name', 'Jane Roe'));
});

test('changing the estimate clears the authorization', function () {
    authorizeAs($this->token);

    $this->actingAs($this->manager)->put(route('estimates.update', $this->estimate), [
        'title' => 'Changed', 'service_type' => 'HVAC repair',
        'lines' => [['name' => 'Labor', 'qty' => 3, 'unit' => 'hr', 'labor' => 100, 'materials' => 0, 'fixed' => null]],
        'pricing' => $this->estimate->pricing,
    ])->assertRedirect();

    $fresh = $this->estimate->fresh();
    expect($fresh->authorized_name)->toBeNull()->and($fresh->authorized_at)->toBeNull();
    expect($this->estimate->activities()->latest('id')->value('action'))->toBe('authorization_cleared');
});

function resave(Estimate $estimate, array $changes = [])
{
    $estimate = $estimate->fresh();

    return test()->put(route('estimates.update', $estimate), array_replace([
        'title' => $estimate->title,
        'service_type' => $estimate->service_type,
        'client' => $estimate->client,
        'address' => $estimate->address,
        'lines' => $estimate->lines,
        'pricing' => $estimate->pricing,
        'scope' => $estimate->scope,
        'terms' => $estimate->terms,
        'notes' => $estimate->notes,
    ], $changes));
}

test('saving with no changes keeps the authorization', function () {
    authorizeAs($this->token);

    $this->actingAs($this->manager);
    resave($this->estimate)->assertRedirect();

    expect($this->estimate->fresh()->authorized_name)->toBe('Jane Roe');
});

test('changing only internal notes keeps the authorization', function () {
    authorizeAs($this->token);

    $this->actingAs($this->manager);
    resave($this->estimate, ['notes' => 'Customer prefers mornings.'])->assertRedirect();

    expect($this->estimate->fresh()->authorized_name)->toBe('Jane Roe')
        ->and($this->estimate->fresh()->notes)->toBe('Customer prefers mornings.');
});

test('changing the terms the customer saw clears the authorization', function () {
    authorizeAs($this->token);

    $this->actingAs($this->manager);
    resave($this->estimate, ['terms' => 'New terms.'])->assertRedirect();

    expect($this->estimate->fresh()->authorized_name)->toBeNull();
});

test('changing markup changes the price and clears the authorization', function () {
    authorizeAs($this->token);

    $this->actingAs($this->manager);
    resave($this->estimate, ['pricing' => [...$this->estimate->pricing, 'markup' => 45]])->assertRedirect();

    expect($this->estimate->fresh()->authorized_name)->toBeNull();
});

test('number formatting alone does not clear the authorization', function () {
    // Stored as whole numbers; the form sends them back as decimals.
    $this->estimate->forceFill(['lines' => [['name' => 'Labor', 'qty' => 2, 'unit' => 'hr', 'labor' => 100, 'materials' => 0, 'fixed' => null]]])->saveQuietly();
    authorizeAs($this->token);

    $this->actingAs($this->manager);
    resave($this->estimate, ['lines' => [['name' => 'Labor', 'qty' => '2.0', 'unit' => 'hr', 'labor' => '100.00', 'materials' => '0', 'fixed' => null]]])
        ->assertRedirect();

    expect($this->estimate->fresh()->authorized_name)->toBe('Jane Roe');
});
