<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Permissions\Models\Permission;
use App\Modules\Roles\DTOs\SyncRolePermissionsData;
use App\Modules\Roles\Exceptions\UnknownPermissionsSelected;
use App\Modules\Roles\Models\Role;

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
