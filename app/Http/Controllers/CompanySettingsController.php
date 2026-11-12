<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanySettingsRequest;
use App\Support\CompanyColors;
use App\Support\CurrentCompany;
use App\Support\ServiceCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CompanySettingsController extends Controller
{
    public function edit(Request $request, CurrentCompany $current): Response|RedirectResponse
    {
        $company = $current->get();

        if (! $company) {
            abort_unless($request->user()->isGlobal(), 403);

            return to_route('companies.index');
        }

        Gate::authorize('update', $company);

        return Inertia::render('company/Settings', [
            'company' => [
                ...$company->summary(),
                'defaultScope' => $company->default_scope ?? '',
                'defaultTerms' => $company->default_terms ?? '',
            ],
            'serviceGroups' => array_map(
                fn (array $g) => ['name' => $g['name'], 'icon' => $g['icon'], 'color' => $g['color'], 'services' => $g['services']],
                app(ServiceCatalog::class)->groupCards(),
            ),
        ]);
    }

    public function update(CompanySettingsRequest $request, CurrentCompany $current): RedirectResponse
    {
        $company = $current->get();
        abort_unless($company !== null, 403);
        Gate::authorize('update', $company);

        $data = $request->validated();
        $disk = Storage::disk('public');

        $attributes = [
            'name' => $data['name'],
            'short_name' => $data['short_name'],
            'location' => $data['location'],
            'color' => strtolower($data['color']),
            'tone' => CompanyColors::tone($data['color']),
            'default_scope' => $data['default_scope'] ?? null,
            'default_terms' => $data['default_terms'] ?? null,
        ];

        if (array_key_exists('services', $data)) {
            $attributes['service_types'] = array_values(array_intersect(app(ServiceCatalog::class)->active(), $data['services']));
        }

        if ($request->hasFile('logo') || $request->boolean('remove_logo')) {
            if ($company->logo_path) {
                $disk->delete($company->logo_path);
            }
            $attributes['logo_path'] = null;
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $attributes['logo_path'] = $file->storeAs(
                "logos/{$company->id}",
                Str::uuid().'.'.$file->extension(),
                'public',
            );
        }

        $company->update($attributes);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Company settings saved.']);

        return back();
    }
}
