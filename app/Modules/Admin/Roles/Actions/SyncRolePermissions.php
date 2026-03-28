<?php

declare(strict_types=1);

namespace App\Modules\Admin\Roles\Actions;

use App\Models\Permission;
use App\Models\Role;
use App\Modules\Admin\Roles\DTOs\SyncRolePermissionsData;
use App\Modules\Admin\Roles\Exceptions\UnknownPermissionsSelected;

final class SyncRolePermissions
{
    public static function handle(Role $role, SyncRolePermissionsData $data): Role
    {
        $requestedPermissions = collect($data->permissions)
            ->unique()
            ->values();

        $existingPermissions = Permission::query()
            ->whereIn('name', $requestedPermissions->all())
            ->pluck('name')
            ->values();

        $missingPermissions = $requestedPermissions
            ->diff($existingPermissions)
            ->values()
            ->all();

        if ($missingPermissions !== []) {
            throw UnknownPermissionsSelected::fromNames($missingPermissions);
        }

        $role->syncPermissions($existingPermissions->all());

        return $role;
    }
}
