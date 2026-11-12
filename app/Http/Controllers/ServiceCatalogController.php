<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Estimate;
use App\Models\ServiceGroup;
use App\Models\ServiceTemplate;
use App\Models\ServiceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin management of trades (service groups) and their services.
 * Estimates, templates and company service lists store service names, so a rename
 * is carried through to them; a service in use can be retired but not deleted.
 */
class ServiceCatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAdmin($request);

        $usage = Estimate::query()->selectRaw('service_type, count(*) as n')->groupBy('service_type')->pluck('n', 'service_type');
        $templateUsage = ServiceTemplate::query()->selectRaw('service_type, count(*) as n')->groupBy('service_type')->pluck('n', 'service_type');

        $companies = Company::query()->orderBy('id')->get();
        $offeredBy = fn (string $service) => $companies
            ->filter(fn (Company $c) => $c->service_types === null || in_array($service, $c->service_types, true))
            ->pluck('short_name')->values()->all();

        $groups = ServiceGroup::query()->with('services')->orderBy('sort')->orderBy('id')->get()
            ->map(fn (ServiceGroup $g) => [
                'id' => $g->id,
                'name' => $g->name,
                'icon' => $g->icon,
                'color' => $g->color,
                'services' => $g->services->map(fn (ServiceType $s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'isActive' => $s->is_active,
                    'usage' => (int) ($usage[$s->name] ?? 0) + (int) ($templateUsage[$s->name] ?? 0),
                    'offeredBy' => $offeredBy($s->name),
                ])->values(),
            ]);

        return Inertia::render('services/Index', [
            'groups' => $groups,
            'icons' => ServiceGroup::ICONS,
        ]);
    }

    public function storeGroup(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate($this->groupRules());

        ServiceGroup::create([...$data, 'color' => strtolower($data['color']), 'sort' => (int) ServiceGroup::max('sort') + 1]);

        Inertia::flash('toast', ['type' => 'success', 'message' => "Trade “{$data['name']}” added."]);

        return back();
    }

    public function updateGroup(Request $request, ServiceGroup $group): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate($this->groupRules($group));

        $group->update([...$data, 'color' => strtolower($data['color'])]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Trade updated.']);

        return back();
    }

    public function storeService(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate($this->serviceRules());

        ServiceType::create([...$data, 'is_active' => true, 'sort' => (int) ServiceType::max('sort') + 1]);

        Inertia::flash('toast', ['type' => 'success', 'message' => "Service “{$data['name']}” added."]);

        return back();
    }

    public function updateService(Request $request, ServiceType $service): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([...$this->serviceRules($service), 'is_active' => ['required', 'boolean']]);

        DB::transaction(function () use ($service, $data) {
            $old = $service->name;
            $service->update($data);

            if ($old !== $service->name) {
                $this->renameEverywhere($old, $service->name);
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Service updated.']);

        return back();
    }

    public function destroyService(Request $request, ServiceType $service): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $inUse = Estimate::where('service_type', $service->name)->exists()
            || ServiceTemplate::where('service_type', $service->name)->exists();

        if ($inUse) {
            throw ValidationException::withMessages([
                'service' => "“{$service->name}” is used by estimates or templates. Retire it instead.",
            ]);
        }

        DB::transaction(function () use ($service) {
            $this->removeFromCompanies($service->name);
            $service->delete();
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Service deleted.']);

        return back();
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()->isGlobal(), 403);
    }

    /** @return array<string, mixed> */
    private function groupRules(?ServiceGroup $group = null): array
    {
        return [
            'name' => ['required', 'string', 'max:60', Rule::unique('service_groups', 'name')->ignore($group?->id)],
            'icon' => ['required', Rule::in(ServiceGroup::ICONS)],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }

    /** @return array<string, mixed> */
    private function serviceRules(?ServiceType $service = null): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('service_types', 'name')->ignore($service?->id)],
            'service_group_id' => ['required', 'integer', 'exists:service_groups,id'],
        ];
    }

    private function renameEverywhere(string $old, string $new): void
    {
        Estimate::where('service_type', $old)->update(['service_type' => $new]);
        ServiceTemplate::where('service_type', $old)->update(['service_type' => $new]);

        Company::whereNotNull('service_types')->each(function (Company $company) use ($old, $new) {
            if (in_array($old, $company->service_types ?? [], true)) {
                $company->update(['service_types' => array_map(fn ($s) => $s === $old ? $new : $s, $company->service_types)]);
            }
        });
    }

    private function removeFromCompanies(string $name): void
    {
        Company::whereNotNull('service_types')->each(function (Company $company) use ($name) {
            if (in_array($name, $company->service_types ?? [], true)) {
                $company->update(['service_types' => array_values(array_diff($company->service_types, [$name]))]);
            }
        });
    }
}
