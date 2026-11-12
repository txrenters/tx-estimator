<?php

namespace App\Actions\Estimates;

use App\Enums\EstimateStatus;
use App\Models\Company;
use App\Models\Estimate;
use App\Models\User;
use App\Support\EstimateRules;
use App\Support\ProposalPresenter;
use Illuminate\Support\Facades\DB;

class SaveEstimate
{
    /**
     * Create or update an estimate. Status only changes as a side effect of editing:
     * an approved estimate goes back to draft, and a declined one saved by an estimator
     * goes back to draft. Explicit status changes go through TransitionEstimate.
     *
     * @param  array<string, mixed>  $data  validated SaveEstimateRequest data
     */
    public function handle(User $by, ?Estimate $estimate, array $data, Company $company): Estimate
    {
        return DB::transaction(function () use ($by, $estimate, $data, $company) {
            $estimate ??= new Estimate([
                'company_id' => $company->id,
                'user_id' => $by->id,
                'status' => EstimateStatus::Draft,
            ]);

            // What the customer saw (and may have signed), before this edit.
            $signedVersion = $estimate->exists && $estimate->authorized_at !== null
                ? ProposalPresenter::customerFacing($estimate)
                : null;

            $estimate->fill([
                'title' => $data['title'],
                'service_type' => $data['service_type'],
                'client' => $data['client'] ?? null,
                'address' => $data['address'] ?? null,
                'scope' => $data['scope'] ?? null,
                'terms' => $data['terms'] ?? null,
                'notes' => $data['notes'] ?? null,
                'lines' => EstimateRules::normalizeLines($data['lines']),
                'pricing' => EstimateRules::normalizePricing($data['pricing']),
            ]);

            $from = $estimate->exists ? $estimate->status : null;

            if ($from === EstimateStatus::Approved || $from === EstimateStatus::Declined) {
                $estimate->status = EstimateStatus::Draft;
                $estimate->approved_by = null;
                $estimate->approved_at = null;
            }

            // A customer signature must not stay on a proposal they didn't see: clear it only
            // when something on the customer's copy changed (not internal notes or costs).
            $clearedAuthorization = $signedVersion !== null
                && $signedVersion != ProposalPresenter::customerFacing($estimate); // by value: 1 and 1.0 are the same quantity
            if ($clearedAuthorization) {
                $estimate->authorized_name = null;
                $estimate->authorized_at = null;
                $estimate->authorized_ip = null;
            }

            $estimate->applyTotals();
            $estimate->save();

            if ($clearedAuthorization) {
                $estimate->logActivity($by, 'authorization_cleared', $estimate->status, $estimate->status);
            }

            if ($from !== null && $from !== $estimate->status) {
                $estimate->logActivity($by, 'reverted_to_draft', $from, $estimate->status);
            }

            $estimate->recordRevision($by);

            return $estimate;
        });
    }
}
