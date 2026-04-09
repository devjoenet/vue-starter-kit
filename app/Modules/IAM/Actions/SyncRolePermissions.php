<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\DTOs\SyncRolePermissionsData;
use App\Modules\IAM\Events\RolePermissionsSynced;
use App\Modules\IAM\Exceptions\UnknownPermissionsSelected;
use App\Modules\IAM\Models\Permission;
use App\Modules\IAM\Models\Role;
use Illuminate\Support\Facades\DB;

final class SyncRolePermissions
{
    public static function handle(Role $role, SyncRolePermissionsData $data): void
    {
        $permissionNames = self::resolvePermissionNames($data);
        $before = $role->permissions()->pluck('name')->sort()->values()->all();

        DB::transaction(function () use ($role, $permissionNames, $before): void {
            $role->syncPermissions($permissionNames);

            event(new RolePermissionsSynced($role, $before, $permissionNames));
        });
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

        return $existingPermissions->all();
    }
}
