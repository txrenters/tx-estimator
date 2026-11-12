<?php

namespace App\Policies;

use App\Enums\EstimateStatus;
use App\Models\Estimate;
use App\Models\User;

class EstimatePolicy
{
    public function view(User $user, Estimate $estimate): bool
    {
        return $user->belongsToCompany($estimate->company_id);
    }

    public function update(User $user, Estimate $estimate): bool
    {
        if (! $this->view($user, $estimate)) {
            return false;
        }

        if ($user->canApprove()) {
            return true;
        }

        return $this->isCreator($user, $estimate)
            && in_array($estimate->status, [EstimateStatus::Draft, EstimateStatus::Pending, EstimateStatus::Declined], true);
    }

    public function delete(User $user, Estimate $estimate): bool
    {
        if (! $this->view($user, $estimate)) {
            return false;
        }

        return $user->canApprove()
            || ($this->isCreator($user, $estimate) && $estimate->status === EstimateStatus::Draft);
    }

    /** Create or turn off a customer share link: approvers, or the estimate's creator. */
    public function share(User $user, Estimate $estimate): bool
    {
        return $this->view($user, $estimate)
            && ($user->canApprove() || $this->isCreator($user, $estimate));
    }

    public function duplicate(User $user, Estimate $estimate): bool
    {
        return $this->view($user, $estimate);
    }

    public function transition(User $user, Estimate $estimate, string $action): bool
    {
        if (! $this->view($user, $estimate)) {
            return false;
        }

        return match ($action) {
            'submit' => $estimate->status === EstimateStatus::Draft
                && ($user->canApprove() || $this->isCreator($user, $estimate)),
            'withdraw' => $estimate->status === EstimateStatus::Pending
                && ($user->canApprove() || $this->isCreator($user, $estimate)),
            'approve', 'decline' => $estimate->status === EstimateStatus::Pending && $user->canApprove(),
            default => false,
        };
    }

    private function isCreator(User $user, Estimate $estimate): bool
    {
        return $estimate->user_id === $user->id;
    }
}
