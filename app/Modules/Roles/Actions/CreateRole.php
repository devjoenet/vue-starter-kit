<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Roles\DTOs\CreateRoleData;
use App\Modules\Roles\Models\Role;

final class CreateRole
{
    public static function handle(CreateRoleData $data): Role
    {
        $role = Role::withTrashed()->firstOrNew([
            'name' => $data->name,
            'guard_name' => 'web',
        ]);

        $role->forceFill([
            'name' => $data->name,
            'guard_name' => 'web',
            'deleted_at' => null,
        ])->save();

        $role->users()->sync($data->user_ids);

        return $role;
    }
}
