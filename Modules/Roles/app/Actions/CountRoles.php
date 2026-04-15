<?php

declare(strict_types=1);

namespace Modules\Roles\Actions;

use Modules\Roles\Models\Role;

final class CountRoles
{
    public static function handle(): int
    {
        return Role::query()->count();
    }
}
