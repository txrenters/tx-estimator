<?php

namespace App\Support;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

/**
 * Resolves which company the signed-in user is working in.
 *
 * Managers and estimators are pinned to their own company. Admins and
 * superadmins pick one (stored in the session) or work across all (null).
 */
class CurrentCompany
{
    public const SESSION_KEY = 'current_company_id';

    private ?Company $resolved = null;

    private ?string $resolvedFor = null;

    public function user(): ?User
    {
        /** @var User|null */
        return auth()->user();
    }

    public function get(): ?Company
    {
        $user = $this->user();
        $key = ($user->id ?? 'guest').'|'.($user?->isGlobal() ? session(self::SESSION_KEY) : $user?->company_id);

        if ($this->resolvedFor === $key) {
            return $this->resolved;
        }

        $this->resolvedFor = $key;

        if (! $user) {
            return $this->resolved = null;
        }

        if (! $user->isGlobal()) {
            return $this->resolved = $user->company;
        }

        $id = session(self::SESSION_KEY);

        return $this->resolved = $id ? Company::query()->whereKey($id)->first() : null;
    }

    public function set(?Company $company): void
    {
        $user = $this->user();

        if (! $user || ! $user->isGlobal()) {
            throw new AuthorizationException('You cannot switch companies.');
        }

        session([self::SESSION_KEY => $company?->id]);
        $this->resolvedFor = null;
    }

    /**
     * The company a new record should belong to.
     */
    public function companyForWrite(?int $requestedId): Company
    {
        $user = $this->user();

        if ($user && ! $user->isGlobal()) {
            return $user->company;
        }

        // A global user's explicit, valid choice wins; otherwise fall back to the selected company.
        $company = ($requestedId ? Company::query()->whereKey($requestedId)->first() : null) ?? $this->get();

        if (! $company) {
            throw ValidationException::withMessages(['company_id' => 'Choose a company.']);
        }

        return $company;
    }

    /**
     * Company ids visible in the current scope; null means every company.
     *
     * @return list<int>|null
     */
    public function visibleCompanyIds(): ?array
    {
        $company = $this->get();

        if ($company) {
            return [$company->id];
        }

        $user = $this->user();

        return $user && $user->isGlobal() ? null : [];
    }
}
