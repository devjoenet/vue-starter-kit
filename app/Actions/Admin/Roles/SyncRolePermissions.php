<?php

declare(strict_types=1);

namespace App\Actions\Admin\Roles;

use App\Models\Permission;
use App\Models\Role;
use App\Support\Data\Admin\Roles\SyncRolePermissionsData;
use App\Support\Exceptions\UnknownPermissionsSelected;

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
