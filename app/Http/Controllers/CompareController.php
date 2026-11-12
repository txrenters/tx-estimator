<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Support\CurrentCompany;
use App\Support\Pricing;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompareController extends Controller
{
    public function __invoke(Request $request, CurrentCompany $current): Response
    {
        $data = $request->validate(['ids' => ['nullable', 'array'], 'ids.*' => ['integer']]);
        $ids = array_slice(array_values(array_unique($data['ids'] ?? [])), 0, 3);

        $scope = fn () => Estimate::query()->inCompanies($current->visibleCompanyIds());

        $options = $scope()->latest('updated_at')->limit(200)
            ->get(['id', 'title', 'number'])
            ->map(fn (Estimate $e) => ['id' => $e->id, 'title' => $e->title, 'number' => $e->number]);

        $selected = $ids ? $scope()->with('company')->whereKey($ids)->get()
            ->sortBy(fn (Estimate $e) => array_search($e->id, $ids))
            ->values()
            ->map(function (Estimate $e) {
                $totals = Pricing::calculate($e->lines, $e->pricing);

                return [
                    'id' => $e->id,
                    'title' => $e->title,
                    'number' => $e->number,
                    'company' => $e->company->short_name,
                    'service' => $e->service_type,
                    'status' => $e->status->value,
                    'cost' => $totals['cost'],
                    'markup' => $e->pricing['markup'],
                    'discount' => $totals['discount'],
                    'trip' => (float) $e->pricing['trip'],
                    'total' => $totals['total'],
                    'margin' => ($totals['incomplete'] || ! $totals['total']) ? null : round($totals['margin'], 1),
                    'target' => $e->pricing['target'],
                ];
            }) : collect();

        return Inertia::render('Compare', [
            'options' => $options,
            'selected' => $selected,
        ]);
    }
}
