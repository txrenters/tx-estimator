<?php

namespace App\Http\Controllers;

use App\Enums\EstimateStatus;
use App\Models\Company;
use App\Models\Estimate;
use App\Models\EstimateActivity;
use App\Support\CurrentCompany;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OverviewController extends Controller
{
    public function __invoke(Request $request, CurrentCompany $current): Response
    {
        abort_unless($request->user()->isGlobal(), 403);

        // Follows the sidebar switcher: one company when selected, otherwise every company.
        $scope = $current->get();
        $ids = $scope ? [$scope->id] : null;

        $byCompany = Estimate::query()->inCompanies($ids)->toBase()
            ->selectRaw('company_id, count(*) as estimates')
            ->selectRaw("sum(case when status = 'pending' then 1 else 0 end) as pending")
            ->selectRaw("sum(case when status = 'declined' then 1 else 0 end) as declined")
            ->selectRaw("sum(case when status = 'approved' then total else 0 end) as approved_value")
            ->selectRaw('avg(margin) as average_margin')
            ->groupBy('company_id')
            ->get()
            ->keyBy('company_id');

        $companies = Company::query()->when($scope, fn ($q) => $q->whereKey($scope->id))->orderBy('id')->get()->map(function (Company $c) use ($byCompany) {
            $row = $byCompany->get($c->id);

            return [
                ...$c->summary(),
                'estimates' => (int) ($row->estimates ?? 0),
                'pending' => (int) ($row->pending ?? 0),
                'approvedValue' => round((float) ($row->approved_value ?? 0), 2),
                'averageMargin' => $row?->average_margin !== null ? round((float) $row->average_margin, 1) : null,
            ];
        });

        $averageMargin = Estimate::query()->inCompanies($ids)->whereNotNull('margin')->avg('margin');

        $pendingQueue = Estimate::with(['company', 'creator'])
            ->inCompanies($ids)
            ->where('status', EstimateStatus::Pending)
            ->oldest('updated_at')
            ->limit(20)
            ->get()
            ->map(fn (Estimate $e) => [
                'id' => $e->id,
                'number' => $e->number,
                'title' => $e->title,
                'company' => $e->company->summary(),
                'creator' => $e->creator->name,
                'total' => (float) $e->total,
                'flags' => $e->flags(),
                'submittedAt' => $e->updated_at?->toIso8601String(),
            ]);

        $activity = EstimateActivity::with(['user', 'estimate', 'company'])
            ->when($ids, fn ($q) => $q->whereIn('company_id', $ids))
            ->latest('id')
            ->limit(20)
            ->get()
            ->map(fn (EstimateActivity $a) => [
                'id' => $a->id,
                'action' => $a->action,
                'user' => $a->user->name ?? ($a->actor_name ? "{$a->actor_name} (customer)" : 'Customer'),
                'estimateId' => $a->estimate_id,
                'number' => $a->estimate->number,
                'title' => $a->estimate->title,
                'company' => $a->company->short_name,
                'at' => $a->created_at->toIso8601String(),
            ]);

        return Inertia::render('Overview', [
            'scope' => $scope?->summary(),
            'tiles' => [
                'total' => (int) $byCompany->sum('estimates'),
                'pending' => (int) $byCompany->sum('pending'),
                'declined' => (int) $byCompany->sum('declined'),
                'approvedValue' => round((float) $byCompany->sum('approved_value'), 2),
                'averageMargin' => $averageMargin !== null ? round((float) $averageMargin, 1) : null,
            ],
            'companies' => $companies,
            'pendingQueue' => $pendingQueue,
            'activity' => $activity,
        ]);
    }
}
