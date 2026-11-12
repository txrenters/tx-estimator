<?php

use App\Enums\EstimateStatus;
use App\Models\Company;
use App\Models\Estimate;
use App\Models\EstimateActivity;
use App\Models\User;

beforeEach(function () {
    $this->air = Company::factory()->create();
    $this->roof = Company::factory()->create();
    $this->estimator = User::factory()->estimator($this->air)->create();
});

function transitionTo(User $user, Estimate $estimate, string $action)
{
    return test()->actingAs($user)->post(route('estimates.status', $estimate), ['action' => $action]);
}

test('estimator submits own draft and the activity is logged', function () {
    $estimate = Estimate::factory()->for($this->air)->for($this->estimator, 'creator')->create();

    transitionTo($this->estimator, $estimate, 'submit')->assertRedirect();

    expect($estimate->fresh()->status)->toBe(EstimateStatus::Pending);
    $activity = EstimateActivity::sole();
    expect($activity->action)->toBe('submitted')
        ->and($activity->from_status)->toBe('draft')
        ->and($activity->to_status)->toBe('pending')
        ->and($activity->user_id)->toBe($this->estimator->id)
        ->and($activity->company_id)->toBe($this->air->id);
});

test('estimator cannot approve', function () {
    $estimate = Estimate::factory()->for($this->air)->for($this->estimator, 'creator')->create(['status' => EstimateStatus::Pending]);

    transitionTo($this->estimator, $estimate, 'approve')->assertForbidden();
    expect($estimate->fresh()->status)->toBe(EstimateStatus::Pending);
});

test('manager approves own company pending estimate', function () {
    $manager = User::factory()->manager($this->air)->create();
    $estimate = Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Pending]);

    transitionTo($manager, $estimate, 'approve')->assertRedirect();

    $fresh = $estimate->fresh();
    expect($fresh->status)->toBe(EstimateStatus::Approved)
        ->and($fresh->approved_by)->toBe($manager->id)
        ->and($fresh->approved_at)->not->toBeNull();
});

test('manager cannot approve another company estimate', function () {
    $manager = User::factory()->manager($this->air)->create();
    $estimate = Estimate::factory()->for($this->roof)->create(['status' => EstimateStatus::Pending]);

    transitionTo($manager, $estimate, 'approve')->assertForbidden();
});

test('admin approves any company', function () {
    $estimate = Estimate::factory()->for($this->roof)->create(['status' => EstimateStatus::Pending]);

    transitionTo(User::factory()->admin()->create(), $estimate, 'approve')->assertRedirect();
    expect($estimate->fresh()->status)->toBe(EstimateStatus::Approved);
});

test('decline records the reviewer', function () {
    $manager = User::factory()->manager($this->air)->create();
    $estimate = Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Pending]);

    transitionTo($manager, $estimate, 'decline')->assertRedirect();

    expect($estimate->fresh()->status)->toBe(EstimateStatus::Declined)
        ->and($estimate->fresh()->approved_by)->toBe($manager->id);
    expect(EstimateActivity::sole()->action)->toBe('declined');
});

test('cannot approve a draft', function () {
    $estimate = Estimate::factory()->for($this->air)->create();

    transitionTo(User::factory()->admin()->create(), $estimate, 'approve')->assertForbidden();
});

test('withdraw returns a pending estimate to draft', function () {
    $estimate = Estimate::factory()->for($this->air)->for($this->estimator, 'creator')->create(['status' => EstimateStatus::Pending]);

    transitionTo($this->estimator, $estimate, 'withdraw')->assertRedirect();
    expect($estimate->fresh()->status)->toBe(EstimateStatus::Draft);
});

test('invalid action is rejected', function () {
    $estimate = Estimate::factory()->for($this->air)->for($this->estimator, 'creator')->create();

    transitionTo($this->estimator, $estimate, 'explode')->assertSessionHasErrors('action');
});

test('editing an approved estimate logs a revert to draft', function () {
    $estimate = Estimate::factory()->for($this->air)->create(['status' => EstimateStatus::Approved]);
    $manager = User::factory()->manager($this->air)->create();

    $this->actingAs($manager)->put(route('estimates.update', $estimate), [
        'title' => 'Changed', 'service_type' => 'HVAC repair',
        'lines' => $estimate->lines, 'pricing' => $estimate->pricing,
    ])->assertRedirect();

    $activity = EstimateActivity::sole();
    expect($activity->action)->toBe('reverted_to_draft')->and($activity->from_status)->toBe('approved');
});
