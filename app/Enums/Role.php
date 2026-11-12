<?php

namespace App\Enums;

enum Role: string
{
    case Superadmin = 'superadmin';
    case Admin = 'admin';
    case Manager = 'manager';
    case Estimator = 'estimator';

    public function label(): string
    {
        return match ($this) {
            self::Superadmin => 'Superadmin',
            self::Admin => 'Admin',
            self::Manager => 'Manager',
            self::Estimator => 'Estimator',
        };
    }

    public function isGlobal(): bool
    {
        return $this === self::Superadmin || $this === self::Admin;
    }
}
