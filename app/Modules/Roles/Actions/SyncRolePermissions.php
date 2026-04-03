<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
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
        $before = $role->permissions()->pluck('name')->sort()->values()->all();

        DB::transaction(function () use ($role, $permissionNames, $before): void {
            $role->syncPermissions($permissionNames);

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: 'roles.permissions_synced',
                summary: sprintf('Updated permissions for role %s.', $role->name),
                subjectType: Role::class,
                subjectId: (int) $role->getKey(),
                subjectLabel: $role->name,
                changes: ['before' => ['permissions' => $before], 'after' => ['permissions' => $permissionNames]],
            )));
        });

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
