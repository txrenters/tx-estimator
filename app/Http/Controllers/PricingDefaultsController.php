<?php

namespace App\Http\Controllers;

use App\Http\Requests\PricingDefaultsRequest;
use App\Support\CurrentCompany;
use App\Support\EstimateRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PricingDefaultsController extends Controller
{
    public function edit(Request $request, CurrentCompany $current): Response
    {
        $company = $current->get();

        return Inertia::render('PricingDefaults', [
            'defaults' => $company->pricing_defaults ?? config('estimating.pricing_defaults'),
            'company' => $company?->summary(),
            'canEdit' => $request->user()->canApprove() && $company !== null,
        ]);
    }

    public function update(PricingDefaultsRequest $request, CurrentCompany $current): RedirectResponse
    {
        $company = $current->get();

        if (! $company) {
            throw ValidationException::withMessages(['company_id' => 'Choose a company before saving pricing defaults.']);
        }

        $company->update(['pricing_defaults' => EstimateRules::normalizePricing($request->validated())]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pricing defaults saved.']);

        return back();
    }
}
