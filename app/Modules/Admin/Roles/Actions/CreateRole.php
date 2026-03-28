<?php

declare(strict_types=1);

namespace App\Modules\Admin\Roles\Actions;

use App\Models\Role;
use App\Modules\Admin\Roles\DTOs\CreateRoleData;

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
