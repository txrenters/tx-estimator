<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Support\ProposalPresenter;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class EstimateProposalController extends Controller
{
    public function __invoke(Estimate $estimate): Response
    {
        Gate::authorize('view', $estimate);

        return Inertia::render('estimates/Proposal', [
            'proposal' => ['id' => $estimate->id, ...ProposalPresenter::present($estimate)],
            'authorization' => $estimate->authorization(),
        ]);
    }
}
