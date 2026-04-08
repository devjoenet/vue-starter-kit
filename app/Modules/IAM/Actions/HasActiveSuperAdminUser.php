<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\Models\Role;

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
