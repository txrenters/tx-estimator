<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use App\Models\ServiceTemplate;
use App\Support\CompanyColors;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Company::class);

        $companies = Company::query()
            ->withCount(['users', 'estimates'])
            ->orderBy('id')
            ->get()
            ->map(fn (Company $c) => [...$c->summary(), 'users' => $c->users_count, 'estimates' => $c->estimates_count]);

        return Inertia::render('companies/Index', ['companies' => $companies]);
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $slug = Str::slug($data['name']);
        $base = $slug;
        for ($i = 2; Company::where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        $source = isset($data['copy_from']) ? Company::query()->whereKey($data['copy_from'])->first() : null;
        $copy = $source ? ($data['copy'] ?? []) : [];

        DB::transaction(function () use ($data, $slug, $source, $copy) {
            $company = Company::create([
                'name' => $data['name'],
                'short_name' => $data['short_name'],
                'location' => $data['location'],
                'slug' => $slug,
                'color' => strtolower($data['color']),
                'tone' => CompanyColors::tone($data['color']),
                'currency' => 'USD',
                'pricing_defaults' => in_array('pricing', $copy, true) ? $source->pricing_defaults : config('estimating.pricing_defaults'),
                'default_scope' => in_array('pricing', $copy, true) ? $source->default_scope : null,
                'default_terms' => in_array('pricing', $copy, true) ? $source->default_terms : null,
                'service_types' => in_array('services', $copy, true) ? $source->service_types : null,
            ]);

            if (in_array('templates', $copy, true)) {
                $source->templates()->orderBy('id')->get()->each(
                    fn (ServiceTemplate $t) => $t->replicate(['key'])->fill(['company_id' => $company->id])->save()
                );
            }
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Company created.']);

        return to_route('companies.index');
    }
}
