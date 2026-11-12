<?php

namespace App\Http\Middleware;

use App\Enums\EstimateStatus;
use App\Models\Company;
use App\Models\User;
use App\Support\CurrentCompany;
use App\Support\ServiceCatalog;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            // Signed-in pages only; public pages (e.g. a shared proposal) get no desk data.
            ...($request->user() ? ['desk' => fn () => $this->desk($request)] : []),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function desk(Request $request): ?array
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return null;
        }

        $current = app(CurrentCompany::class)->get();
        $global = $user->isGlobal();

        $companies = $global
            ? Company::orderBy('id')->get()
            : Company::whereKey($user->company_id)->get();

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'roleLabel' => $user->role->label(),
                'companyId' => $user->company_id,
            ],
            'companies' => $companies->map->summary()->values(),
            'currentCompany' => $current?->summary(),
            'can' => [
                'switchCompany' => $global,
                'viewOverview' => $global,
                'manageUsers' => $user->canApprove(),
                'manageCompany' => $user->canApprove(),
                'approve' => $user->canApprove(),
                'editCatalog' => $user->canApprove(),
                'manageCompanies' => $global,
            ],
            'serviceTypes' => app(ServiceCatalog::class)->active(),
            'statuses' => EstimateStatus::options(),
        ];
    }
}
