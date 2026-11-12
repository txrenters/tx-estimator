<?php

namespace App\Policies;

use App\Models\ServiceTemplate;
use App\Models\User;

class ServiceTemplatePolicy
{
    public function view(User $user, ServiceTemplate $template): bool
    {
        return $user->belongsToCompany($template->company_id);
    }

    public function create(User $user): bool
    {
        return $user->canApprove();
    }

    public function update(User $user, ServiceTemplate $template): bool
    {
        return $user->canApprove() && $user->belongsToCompany($template->company_id);
    }

    public function delete(User $user, ServiceTemplate $template): bool
    {
        return $this->update($user, $template);
    }
}
