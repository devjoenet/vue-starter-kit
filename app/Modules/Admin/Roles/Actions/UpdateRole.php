<?php

declare(strict_types=1);

namespace App\Modules\Admin\Roles\Actions;

use App\Models\Role;
use App\Modules\Admin\Roles\DTOs\UpdateRoleData;

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
