<?php

declare(strict_types=1);

namespace App\Modules\Admin\Roles\Actions;

use App\Models\Role;

final class DeleteRole
{
    public static function handle(Role $role): void
    {
        $role->delete();
    }
}
