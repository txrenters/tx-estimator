<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isGlobal();
    }

    public function create(User $user): bool
    {
        return $user->isGlobal();
    }

    public function update(User $user, Company $company): bool
    {
        return $user->canApprove() && $user->belongsToCompany($company->id);
    }
}
