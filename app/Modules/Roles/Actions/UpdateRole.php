<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Roles\DTOs\UpdateRoleData;
use App\Modules\Roles\Models\Role;

final class UpdateRole
{
    public static function handle(Role $role, UpdateRoleData $data): Role
    {
        $role->forceFill([
            'name' => $data->name,
        ])->save();

        return $role;
    }
}
