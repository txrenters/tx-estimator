<?php

use App\Models\Company;
use App\Models\Estimate;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->air = Company::factory()->create(['name' => 'Crystal Creek Air']);
    $this->manager = User::factory()->manager($this->air)->create();
    $this->estimate = Estimate::factory()->for($this->air)->create([
        'title' => 'Coil replacement',
        'notes' => 'Secret vendor quote',
        'lines' => [['name' => 'Labor', 'qty' => 2, 'unit' => 'hr', 'labor' => 100, 'materials' => 50, 'fixed' => null]],
    ]);
});

function shareUrl(Estimate $estimate): string
{
    return $estimate->fresh()->shareUrl();
}

test('a manager creates a share link that never expires by default', function () {
    $this->actingAs($this->manager)->post(route('estimates.share', $this->estimate))->assertRedirect();

    $fresh = $this->estimate->fresh();
    expect($fresh->share_token)->toHaveLength(12)->toMatch('/^[A-Za-z0-9]{12}$/')
        ->and($fresh->share_expires_at)->toBeNull();

    $this->actingAs($this->manager)->get(route('estimates.edit', $this->estimate))
        ->assertInertia(fn (Assert $page) => $page
            ->where('share.url', shareUrl($this->estimate))
            ->where('share.expiresAt', null)
            ->where('share.views', 0)
            ->where('can.share', true));
});

test('a share link can expire after a set number of days', function () {
    $this->actingAs($this->manager)->post(route('estimates.share', $this->estimate), ['expires_in_days' => 30]);

    expect($this->estimate->fresh()->share_expires_at->isSameDay(now()->addDays(30)))->toBeTrue();
});

test('only the offered expiry options are accepted', function () {
    $this->actingAs($this->manager)->post(route('estimates.share', $this->estimate), ['expires_in_days' => 5])
        ->assertSessionHasErrors('expires_in_days');
});

test('the customer opens the link without logging in and sees no costs or notes', function () {
    // A visitor who has never logged in (no session at all) opens an existing link.
    $this->estimate->forceFill(['share_token' => str_repeat('c', 40)])->saveQuietly();

    $this->get(shareUrl($this->estimate))
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/Proposal')
            ->where('proposal.title', 'Coil replacement')
            ->where('proposal.company.name', 'Crystal Creek Air')
            ->where('proposal.lines.0', ['name' => 'Labor', 'qty' => 2, 'unit' => 'hr', 'amount' => 390])
            ->missing('proposal.notes')
            ->missing('proposal.id')
            ->missing('desk'));
});

test('opening the link counts the view', function () {
    $this->actingAs($this->manager)->post(route('estimates.share', $this->estimate));
    actAsCustomer();

    $this->get(shareUrl($this->estimate));
    $this->get(shareUrl($this->estimate));

    $fresh = $this->estimate->fresh();
    expect($fresh->share_views)->toBe(2)->and($fresh->share_viewed_at)->not->toBeNull();
});

test('a turned off link stops working', function () {
    $this->actingAs($this->manager)->post(route('estimates.share', $this->estimate));
    $url = shareUrl($this->estimate);

    $this->actingAs($this->manager)->delete(route('estimates.unshare', $this->estimate))->assertRedirect();
    actAsCustomer();

    $this->get($url)->assertNotFound();
});

test('an expired link stops working', function () {
    $this->actingAs($this->manager)->post(route('estimates.share', $this->estimate), ['expires_in_days' => 7]);
    $url = shareUrl($this->estimate);
    actAsCustomer();

    $this->travel(8)->days();

    $this->get($url)->assertNotFound();
});

test('making a new link retires the old one', function () {
    $this->actingAs($this->manager)->post(route('estimates.share', $this->estimate));
    $old = shareUrl($this->estimate);

    $this->actingAs($this->manager)->post(route('estimates.share', $this->estimate));
    actAsCustomer();

    $this->get($old)->assertNotFound();
    $this->get(shareUrl($this->estimate))->assertOk();
});

test('an unknown link is not found', function () {
    $this->get('/p/'.str_repeat('a', 40))->assertNotFound();
});

test('estimators share their own estimates only', function () {
    $estimator = User::factory()->estimator($this->air)->create();
    $own = Estimate::factory()->for($this->air)->for($estimator, 'creator')->create();

    $this->actingAs($estimator)->post(route('estimates.share', $own))->assertRedirect();
    $this->actingAs($estimator)->post(route('estimates.share', $this->estimate))->assertForbidden();
});

test('another company cannot share or turn off the link', function () {
    $other = User::factory()->manager(Company::factory()->create())->create();

    $this->actingAs($other)->post(route('estimates.share', $this->estimate))->assertForbidden();
    $this->actingAs($other)->delete(route('estimates.unshare', $this->estimate))->assertForbidden();
});

test('sharing and turning off are recorded in the activity log', function () {
    $this->actingAs($this->manager)->post(route('estimates.share', $this->estimate));
    $this->actingAs($this->manager)->delete(route('estimates.unshare', $this->estimate));

    expect($this->estimate->activities()->pluck('action')->all())->toBe(['shared', 'unshared']);
});

test('the link starts with the company short name', function () {
    $this->air->update(['short_name' => 'Air']);
    $this->actingAs($this->manager)->post(route('estimates.share', $this->estimate));

    $token = $this->estimate->fresh()->share_token;
    expect(shareUrl($this->estimate))->toBe(url("/p/air/{$token}"));
});

test('a multi-word short name becomes a tidy link part', function () {
    $this->air->update(['short_name' => 'Pest Control']);
    $this->actingAs($this->manager)->post(route('estimates.share', $this->estimate));

    expect(shareUrl($this->estimate))->toContain('/p/pest-control/');
});

test('an old style link without the company redirects to the new one', function () {
    $this->air->update(['short_name' => 'Air']);
    $this->estimate->forceFill(['share_token' => 'abcDEF123456'])->saveQuietly();

    $this->get('/p/abcDEF123456')->assertRedirect(url('/p/air/abcDEF123456'))->assertStatus(301);
});

test('a link with an outdated company name redirects to the current one', function () {
    $this->air->update(['short_name' => 'Air']);
    $this->estimate->forceFill(['share_token' => 'abcDEF123456'])->saveQuietly();

    $this->get('/p/old-name/abcDEF123456')->assertRedirect(url('/p/air/abcDEF123456'))->assertStatus(301);
});

test('the company part alone cannot open another estimate', function () {
    $this->air->update(['short_name' => 'Air']);

    $this->get('/p/air/zzzzzzzzzzzz')->assertNotFound();
});
