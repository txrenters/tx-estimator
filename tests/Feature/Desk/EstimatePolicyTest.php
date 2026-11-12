<?php

use App\Enums\EstimateStatus;
use App\Models\Company;
use App\Models\Estimate;
use App\Models\User;

/**
 * @return array{0: User, 1: Estimate}
 */
function policyScenario(string $role, bool $sameCompany, bool $isCreator, EstimateStatus $status): array
{
    $home = Company::factory()->create();
    $other = Company::factory()->create();

    $actor = match ($role) {
        'superadmin' => User::factory()->superadmin()->create(),
        'admin' => User::factory()->admin()->create(),
        'manager' => User::factory()->manager($home)->create(),
        'estimator' => User::factory()->estimator($home)->create(),
    };

    $company = $sameCompany ? $home : $other;
    $creator = $isCreator ? $actor : User::factory()->estimator($company)->create();

    $estimate = Estimate::factory()->for($company)->for($creator, 'creator')->create(['status' => $status]);

    return [$actor, $estimate];
}

test('view', function (string $role, bool $same, bool $expected) {
    [$actor, $estimate] = policyScenario($role, $same, false, EstimateStatus::Draft);
    expect($actor->can('view', $estimate))->toBe($expected);
})->with([
    ['superadmin', false, true], ['admin', false, true],
    ['manager', true, true], ['manager', false, false],
    ['estimator', true, true], ['estimator', false, false],
]);

test('update', function (string $role, bool $same, bool $creator, EstimateStatus $status, bool $expected) {
    [$actor, $estimate] = policyScenario($role, $same, $creator, $status);
    expect($actor->can('update', $estimate))->toBe($expected);
})->with([
    'admin other company approved' => ['admin', false, false, EstimateStatus::Approved, true],
    'manager own company anyone' => ['manager', true, false, EstimateStatus::Approved, true],
    'manager other company' => ['manager', false, false, EstimateStatus::Draft, false],
    'estimator own draft' => ['estimator', true, true, EstimateStatus::Draft, true],
    'estimator own pending' => ['estimator', true, true, EstimateStatus::Pending, true],
    'estimator own declined' => ['estimator', true, true, EstimateStatus::Declined, true],
    'estimator own approved' => ['estimator', true, true, EstimateStatus::Approved, false],
    'estimator colleague draft' => ['estimator', true, false, EstimateStatus::Draft, false],
]);

test('delete', function (string $role, bool $creator, EstimateStatus $status, bool $expected) {
    [$actor, $estimate] = policyScenario($role, true, $creator, $status);
    expect($actor->can('delete', $estimate))->toBe($expected);
})->with([
    'manager approved' => ['manager', false, EstimateStatus::Approved, true],
    'estimator own draft' => ['estimator', true, EstimateStatus::Draft, true],
    'estimator own pending' => ['estimator', true, EstimateStatus::Pending, false],
    'estimator colleague draft' => ['estimator', false, EstimateStatus::Draft, false],
]);

test('transition', function (string $role, bool $same, bool $creator, EstimateStatus $status, string $action, bool $expected) {
    [$actor, $estimate] = policyScenario($role, $same, $creator, $status);
    expect($actor->can('transition', [$estimate, $action]))->toBe($expected);
})->with([
    'estimator submits own draft' => ['estimator', true, true, EstimateStatus::Draft, 'submit', true],
    'estimator submits colleague draft' => ['estimator', true, false, EstimateStatus::Draft, 'submit', false],
    'estimator cannot approve' => ['estimator', true, true, EstimateStatus::Pending, 'approve', false],
    'estimator withdraws own pending' => ['estimator', true, true, EstimateStatus::Pending, 'withdraw', true],
    'manager approves pending' => ['manager', true, false, EstimateStatus::Pending, 'approve', true],
    'manager declines pending' => ['manager', true, false, EstimateStatus::Pending, 'decline', true],
    'manager cannot approve draft' => ['manager', true, false, EstimateStatus::Draft, 'approve', false],
    'manager other company' => ['manager', false, false, EstimateStatus::Pending, 'approve', false],
    'admin approves anywhere' => ['admin', false, false, EstimateStatus::Pending, 'approve', true],
    'unknown action' => ['admin', true, false, EstimateStatus::Pending, 'explode', false],
]);
