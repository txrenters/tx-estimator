<?php

namespace App\Actions\Estimates;

use App\Enums\EstimateStatus;
use App\Models\Estimate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

class TransitionEstimate
{
    public const ACTIONS = ['submit', 'withdraw', 'approve', 'decline'];

    public function handle(User $by, Estimate $estimate, string $action): Estimate
    {
        Gate::forUser($by)->authorize('transition', [$estimate, $action]);

        [$to, $logged] = match ($action) {
            'submit' => [EstimateStatus::Pending, 'submitted'],
            'withdraw' => [EstimateStatus::Draft, 'withdrawn'],
            'approve' => [EstimateStatus::Approved, 'approved'],
            'decline' => [EstimateStatus::Declined, 'declined'],
            default => throw new InvalidArgumentException("Unknown estimate action [{$action}]."),
        };

        return DB::transaction(function () use ($by, $estimate, $to, $logged) {
            $from = $estimate->status;

            $estimate->status = $to;
            $reviewed = in_array($to, [EstimateStatus::Approved, EstimateStatus::Declined], true);
            $estimate->approved_by = $reviewed ? $by->id : null;
            $estimate->approved_at = $reviewed ? now() : null;
            $estimate->save();

            $estimate->logActivity($by, $logged, $from, $to);
            $estimate->recordRevision($by);

            return $estimate;
        });
    }
}
