<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Roles\Models\Role;

final class HasActiveSuperAdminUser
{
    public static function handle(): bool
    {
        return Role::query()
            ->where('name', EnsureSuperAdminRole::name())
            ->where('guard_name', EnsureSuperAdminRole::guardName())
            ->whereHas('users')
            ->exists();
    }
}
