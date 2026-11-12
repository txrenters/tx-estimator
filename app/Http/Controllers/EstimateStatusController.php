<?php

namespace App\Http\Controllers;

use App\Actions\Estimates\TransitionEstimate;
use App\Models\Estimate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class EstimateStatusController extends Controller
{
    private const MESSAGES = [
        'submit' => 'Estimate submitted for approval.',
        'withdraw' => 'Estimate returned to draft.',
        'approve' => 'Estimate approved.',
        'decline' => 'Estimate declined.',
    ];

    public function __invoke(Request $request, Estimate $estimate, TransitionEstimate $transition): RedirectResponse
    {
        $data = $request->validate(['action' => ['required', Rule::in(TransitionEstimate::ACTIONS)]]);

        $transition->handle($request->user(), $estimate, $data['action']);

        Inertia::flash('toast', ['type' => 'success', 'message' => self::MESSAGES[$data['action']]]);

        return back();
    }
}
