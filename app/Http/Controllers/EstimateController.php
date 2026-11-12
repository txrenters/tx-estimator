<?php

namespace App\Http\Controllers;

use App\Actions\Estimates\SaveEstimate;
use App\Enums\EstimateStatus;
use App\Http\Requests\SaveEstimateRequest;
use App\Models\Company;
use App\Models\Estimate;
use App\Models\EstimateRevision;
use App\Models\ServiceTemplate;
use App\Models\User;
use App\Support\CurrentCompany;
use App\Support\EstimateRules;
use App\Support\ServiceCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class EstimateController extends Controller
{
    public function index(Request $request, CurrentCompany $current): Response
    {
        $filters = $request->validate([
            'company' => ['nullable', 'integer'],
            'service' => ['nullable', 'string', Rule::in(app(ServiceCatalog::class)->all())],
            'status' => ['nullable', Rule::enum(EstimateStatus::class)],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $ids = $current->visibleCompanyIds();
        if ($ids === null && ! empty($filters['company'])) {
            $ids = [(int) $filters['company']];
        }

        $scoped = fn () => Estimate::query()->inCompanies($ids);

        $estimates = $scoped()
            ->with('company')
            ->when($filters['service'] ?? null, fn ($q, $s) => $q->where('service_type', $s))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['q'] ?? null, fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('title', 'like', "%{$term}%")
                ->orWhere('number', 'like', "%{$term}%")
                ->orWhere('client', 'like', "%{$term}%")))
            ->latest('updated_at')
            ->paginate(25)
            ->withQueryString()
            ->through(fn (Estimate $e) => [
                'id' => $e->id,
                'number' => $e->number,
                'title' => $e->title,
                'client' => $e->client,
                'company' => $e->company->summary(),
                'status' => $e->status->value,
                'total' => (float) $e->total,
                'updatedAt' => $e->updated_at?->toIso8601String(),
                'flags' => $e->flags(),
            ]);

        $templateCount = ServiceTemplate::query()->inCompanies($ids)->count();

        return Inertia::render('estimates/Index', [
            'estimates' => $estimates,
            'stats' => [
                'total' => $scoped()->count(),
                'pending' => $scoped()->where('status', EstimateStatus::Pending)->count(),
                'approvedValue' => (float) $scoped()->where('status', EstimateStatus::Approved)->sum('total'),
                'templates' => $templateCount,
            ],
            'filters' => [
                'company' => $filters['company'] ?? '',
                'service' => $filters['service'] ?? '',
                'status' => $filters['status'] ?? '',
                'q' => $filters['q'] ?? '',
            ],
        ]);
    }

    public function create(Request $request, CurrentCompany $current): Response
    {
        $template = null;
        if ($request->filled('template')) {
            $template = ServiceTemplate::findOrFail($request->integer('template'));
            Gate::authorize('view', $template);
        }

        $company = $template->company ?? $current->get();
        $pricing = $company->pricing_defaults ?? config('estimating.pricing_defaults');

        $serviceType = $template->service_type
            ?? ($company ? ServiceTemplate::where('company_id', $company->id)->orderBy('id')->value('service_type') : null)
            ?? ($company?->serviceTypes()[0] ?? app(ServiceCatalog::class)->active()[0] ?? '');

        return Inertia::render('estimates/Builder', [
            'estimate' => [
                'id' => null,
                'number' => null,
                'title' => $template->title ?? '',
                'status' => EstimateStatus::Draft->value,
                'company_id' => $company?->id,
                'service_type' => $serviceType,
                'client' => '',
                'address' => '',
                'lines' => $template->lines ?? [['name' => '', 'qty' => 1, 'unit' => 'job', 'labor' => 0, 'materials' => 0, 'fixed' => null]],
                'pricing' => $pricing,
                'scope' => $template?->scope ?: ($company->default_scope ?? ''),
                'terms' => $template?->terms ?: ($company->default_terms ?? ''),
                'notes' => '',
                'creator' => $request->user()->name,
                'authorization' => null,
                'approver' => null,
                'approvedAt' => null,
                'updatedAt' => null,
            ],
            'can' => ['update' => true, 'delete' => false, 'duplicate' => false, 'submit' => false, 'withdraw' => false, 'approve' => false, 'decline' => false, 'share' => false],
            'share' => null,
            'companyDefaults' => $this->companyDefaults($request->user()),
            'revisions' => [],
        ]);
    }

    public function store(SaveEstimateRequest $request, CurrentCompany $current, SaveEstimate $save): RedirectResponse
    {
        $company = $current->companyForWrite($request->integer('company_id') ?: null);
        abort_unless($request->user()->belongsToCompany($company->id), 403);
        EstimateRules::ensureServiceFor($company, $request->validated('service_type'));

        $estimate = $save->handle($request->user(), null, $request->validated(), $company);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Estimate saved.']);

        return to_route('estimates.edit', $estimate);
    }

    public function edit(Request $request, Estimate $estimate): Response
    {
        Gate::authorize('view', $estimate);

        $user = $request->user();
        $estimate->load(['creator', 'approver']);

        return Inertia::render('estimates/Builder', [
            'estimate' => $this->present($estimate),
            'can' => [
                'update' => $user->can('update', $estimate),
                'delete' => $user->can('delete', $estimate),
                'duplicate' => $user->can('duplicate', $estimate),
                'submit' => $user->can('transition', [$estimate, 'submit']),
                'withdraw' => $user->can('transition', [$estimate, 'withdraw']),
                'approve' => $user->can('transition', [$estimate, 'approve']),
                'decline' => $user->can('transition', [$estimate, 'decline']),
                'share' => $user->can('share', $estimate),
            ],
            'share' => $estimate->share_token && ($estimate->share_expires_at === null || $estimate->share_expires_at->isFuture()) ? [
                'url' => $estimate->shareUrl(),
                'expiresAt' => $estimate->share_expires_at?->toIso8601String(),
                'sharedAt' => $estimate->shared_at?->toIso8601String(),
                'views' => $estimate->share_views,
                'viewedAt' => $estimate->share_viewed_at?->toIso8601String(),
            ] : null,
            'companyDefaults' => $this->companyDefaults($user),
            'revisions' => $this->revisions($estimate),
        ]);
    }

    public function update(SaveEstimateRequest $request, Estimate $estimate, SaveEstimate $save): RedirectResponse
    {
        Gate::authorize('update', $estimate);
        EstimateRules::ensureServiceFor($estimate->company, $request->validated('service_type'), $estimate->service_type);

        $save->handle($request->user(), $estimate, $request->validated(), $estimate->company);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Estimate saved.']);

        return to_route('estimates.edit', $estimate);
    }

    public function destroy(Estimate $estimate): RedirectResponse
    {
        Gate::authorize('delete', $estimate);

        $estimate->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Estimate deleted.']);

        return to_route('estimates.index');
    }

    public function duplicate(Request $request, Estimate $estimate): RedirectResponse
    {
        Gate::authorize('duplicate', $estimate);

        $copy = $estimate->replicate(['number', 'status', 'approved_by', 'approved_at', 'user_id']);
        $copy->title = $estimate->title.' (copy)';
        $copy->status = EstimateStatus::Draft;
        $copy->user_id = $request->user()->id;
        $copy->save();
        $copy->recordRevision($request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Estimate duplicated.']);

        return to_route('estimates.edit', $copy);
    }

    /** @return array<string, mixed> */
    private function present(Estimate $estimate): array
    {
        return [
            'id' => $estimate->id,
            'number' => $estimate->number,
            'title' => $estimate->title,
            'status' => $estimate->status->value,
            'company_id' => $estimate->company_id,
            'service_type' => $estimate->service_type,
            'client' => $estimate->client ?? '',
            'address' => $estimate->address ?? '',
            'lines' => $estimate->lines,
            'pricing' => $estimate->pricing,
            'scope' => $estimate->scope ?? '',
            'terms' => $estimate->terms ?? '',
            'notes' => $estimate->notes ?? '',
            'creator' => $estimate->creator->name,
            'approver' => $estimate->approver?->name,
            'approvedAt' => $estimate->approved_at?->toIso8601String(),
            'updatedAt' => $estimate->updated_at?->toIso8601String(),
            'authorization' => $estimate->authorization(),
        ];
    }

    /**
     * Pricing defaults per company the user may create estimates in, so the builder
     * can reset pricing when a global user picks a different company.
     *
     * @return array<int, array<string, int|float>>
     */
    private function companyDefaults(User $user): array
    {
        $companies = $user->isGlobal() ? Company::all() : Company::whereKey($user->company_id)->get();

        return $companies->mapWithKeys(fn (Company $c) => [$c->id => $c->pricing_defaults])->all();
    }

    /** @return array<int, array<string, mixed>> newest first */
    private function revisions(Estimate $estimate): array
    {
        return $estimate->revisions()->with('user')->latest('id')->limit(50)->get()
            ->map(fn (EstimateRevision $r) => [
                'id' => $r->id,
                'createdAt' => $r->created_at->toIso8601String(),
                'user' => $r->user->name,
                'status' => $r->snapshot['status'],
                'total' => (float) ($r->snapshot['total'] ?? 0),
                'snapshot' => $r->snapshot,
            ])->all();
    }
}
