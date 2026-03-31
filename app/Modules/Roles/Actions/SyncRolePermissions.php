<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Permissions\Models\Permission;
use App\Modules\Roles\DTOs\SyncRolePermissionsData;
use App\Modules\Roles\Exceptions\UnknownPermissionsSelected;
use App\Modules\Roles\Models\Role;
use Illuminate\Support\Facades\DB;

final class SyncRolePermissions
{
    public static function handle(Role $role, SyncRolePermissionsData $data): Role
    {
        $permissionNames = self::resolvePermissionNames($data);

        DB::transaction(fn (): Role => tap($role, fn (Role $role): Role => $role->syncPermissions($permissionNames)));

        return $role;
    }

    /** @return list<string> */
    private static function resolvePermissionNames(SyncRolePermissionsData $data): array
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

        /** @var list<string> $permissionNames */
        $permissionNames = $existingPermissions->all();

        return $permissionNames;
    }
}
