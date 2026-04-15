<?php

declare(strict_types=1);

namespace Modules\Roles\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Permissions\Exceptions\CannotRemoveRequiredSuperAdminPermissions;
use Modules\Permissions\Exceptions\UnknownPermissionsSelected;
use Modules\Permissions\Models\Permission;
use Modules\Roles\DTOs\SyncRolePermissionsData;
use Modules\Roles\Events\RolePermissionsSynced;
use Modules\Roles\Models\Role;

final class SyncRolePermissions
{
    public static function handle(Role $role, SyncRolePermissionsData $data): void
    {
        $permissionNames = self::resolvePermissionNames($data);
        self::ensureProtectedRoleKeepsEveryPermission($role, $permissionNames);
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
            ->sort()
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

    /** @param  list<string>  $permissionNames */
    private static function ensureProtectedRoleKeepsEveryPermission(Role $role, array $permissionNames): void
    {
        if (! EnsureSuperAdminRole::is($role)) {
            return;
        }

        $requiredPermissionNames = EnsureSuperAdminRole::permissionNames();

        if ($permissionNames === $requiredPermissionNames) {
            return;
        }

        throw CannotRemoveRequiredSuperAdminPermissions::forRole($role, $permissionNames, $requiredPermissionNames);
    }
}
