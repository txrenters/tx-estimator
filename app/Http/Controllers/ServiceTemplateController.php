<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveTemplateRequest;
use App\Models\ServiceTemplate;
use App\Support\CurrentCompany;
use App\Support\EstimateRules;
use App\Support\ServiceCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ServiceTemplateController extends Controller
{
    public function index(Request $request, CurrentCompany $current): Response
    {
        $filters = $request->validate([
            'service' => ['nullable', 'string', Rule::in(app(ServiceCatalog::class)->all())],
            'company' => ['nullable', 'integer'],
            'group' => ['nullable', 'string', Rule::in(array_keys(app(ServiceCatalog::class)->groups()))],
        ]);

        $ids = $current->visibleCompanyIds();
        if ($ids === null && ! empty($filters['company'])) {
            $ids = [(int) $filters['company']];
        }

        $templates = ServiceTemplate::query()
            ->with('company')
            ->inCompanies($ids)
            ->when($filters['service'] ?? null, fn ($q, $s) => $q->where('service_type', $s))
            ->when($filters['group'] ?? null, fn ($q, $g) => $q->whereIn('service_type', app(ServiceCatalog::class)->groups()[$g]))
            ->orderBy('company_id')->orderBy('id')
            ->get()
            ->map(fn (ServiceTemplate $t) => [
                'id' => $t->id,
                'title' => $t->title,
                'service' => $t->service_type,
                'scope' => $t->scope,
                'company' => $t->company->summary(),
                'canEdit' => $request->user()->can('update', $t),
            ]);

        return Inertia::render('templates/Index', [
            'templates' => $templates,
            'filters' => ['service' => $filters['service'] ?? '', 'company' => $filters['company'] ?? '', 'group' => $filters['group'] ?? ''],
            'canEdit' => $request->user()->can('create', ServiceTemplate::class),
        ]);
    }

    public function create(Request $request, CurrentCompany $current): Response
    {
        Gate::authorize('create', ServiceTemplate::class);

        return Inertia::render('templates/Edit', [
            'template' => [
                'id' => null,
                'title' => '',
                'service_type' => $request->string('service')->toString() ?: ($current->get()?->serviceTypes()[0] ?? app(ServiceCatalog::class)->active()[0] ?? ''),
                'scope' => '',
                'terms' => '',
                'company_id' => $current->get()?->id,
                'lines' => [['name' => '', 'qty' => 1, 'unit' => 'job', 'labor' => 0, 'materials' => 0, 'fixed' => null]],
            ],
            'markup' => $current->get()?->pricing_defaults['markup'] ?? config('estimating.pricing_defaults.markup'),
        ]);
    }

    public function store(SaveTemplateRequest $request, CurrentCompany $current): RedirectResponse
    {
        Gate::authorize('create', ServiceTemplate::class);

        $company = $current->companyForWrite($request->integer('company_id') ?: null);
        EstimateRules::ensureServiceFor($company, $request->validated('service_type'));

        ServiceTemplate::create([...$this->attributes($request), 'company_id' => $company->id]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Template saved.']);

        return to_route('templates.index');
    }

    public function edit(ServiceTemplate $template): Response
    {
        Gate::authorize('update', $template);

        return Inertia::render('templates/Edit', [
            'template' => [
                'id' => $template->id,
                'title' => $template->title,
                'service_type' => $template->service_type,
                'scope' => $template->scope ?? '',
                'terms' => $template->terms ?? '',
                'company_id' => $template->company_id,
                'lines' => $template->lines,
            ],
            'markup' => $template->company->pricing_defaults['markup'] ?? config('estimating.pricing_defaults.markup'),
        ]);
    }

    public function update(SaveTemplateRequest $request, ServiceTemplate $template): RedirectResponse
    {
        Gate::authorize('update', $template);
        EstimateRules::ensureServiceFor($template->company, $request->validated('service_type'), $template->service_type);

        $template->update($this->attributes($request));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Template rates updated.']);

        return to_route('templates.index');
    }

    public function destroy(ServiceTemplate $template): RedirectResponse
    {
        Gate::authorize('delete', $template);

        $template->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Template deleted.']);

        return to_route('templates.index');
    }

    /** @return array<string, mixed> */
    private function attributes(SaveTemplateRequest $request): array
    {
        $data = $request->validated();

        return [
            'title' => $data['title'],
            'service_type' => $data['service_type'],
            'scope' => $data['scope'] ?? null,
            'terms' => $data['terms'] ?? null,
            'lines' => EstimateRules::normalizeLines($data['lines']),
        ];
    }
}
