<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->canApprove();
    }

    public function create(User $actor): bool
    {
        return $actor->canApprove();
    }

    /** Whether the actor may manage this existing user at all. */
    public function update(User $actor, User $target): bool
    {
        if ($actor->isSuperadmin()) {
            return true;
        }

        if ($target->role->isGlobal()) {
            return false; // Only superadmins manage admins and superadmins.
        }

        if ($actor->isGlobal()) {
            return true;
        }

        return $actor->role === Role::Manager && $actor->company_id === $target->company_id;
    }

    public function toggleActive(User $actor, User $target): bool
    {
        return $actor->id !== $target->id && $this->update($actor, $target);
    }

    /**
     * Roles the actor may assign.
     *
     * @return list<Role>
     */
    public static function assignableRoles(User $actor): array
    {
        return match (true) {
            $actor->isSuperadmin() => Role::cases(),
            $actor->isGlobal(), $actor->role === Role::Manager => [Role::Manager, Role::Estimator],
            default => [],
        };
    }
}
