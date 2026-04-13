<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Actions;

use App\Modules\IAM\Roles\Models\Role;

final class CountRoles
{
    public static function handle(): int
    {
        return Role::query()->count();
    }
}
