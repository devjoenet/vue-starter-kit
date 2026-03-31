<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Roles\Models\Role;

final class DeleteRole
{
    public static function handle(Role $role): void
    {
        $role->delete();
    }
}
