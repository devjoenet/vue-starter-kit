<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\Permissions\DTOs\CreatePermissionData;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\PermissionGroup;
use Illuminate\Support\Facades\DB;

final class CreatePermission
{
    public static function handle(
        CreatePermissionData $data,
        PermissionGroupCatalogContract $permissionGroupCatalog,
    ): Permission {
        $existingPermission = Permission::withTrashed()
            ->where('name', $data->name)
            ->where('guard_name', 'web')
            ->first();

        $permission = DB::transaction(function () use ($data, $permissionGroupCatalog, $existingPermission): Permission {
            $group = self::upsertGroup($data, $permissionGroupCatalog);
            $permission = self::restoreOrCreatePermission($data, $group);

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: $existingPermission?->trashed() ? 'permissions.restored' : 'permissions.created',
                summary: ($existingPermission?->trashed() ? 'Restored' : 'Created').sprintf(' permission %s.', $permission->name),
                subjectType: Permission::class,
                subjectId: (int) $permission->getKey(),
                subjectLabel: $permission->name,
                changes: [
                    'before' => $existingPermission instanceof Permission ? self::auditState($existingPermission) : null,
                    'after' => self::auditState($permission->loadMissing('permissionGroup')),
                ],
            )));

            return $permission;
        });

        return $permission->load('permissionGroup');
    }

    private static function upsertGroup(
        CreatePermissionData $data,
        PermissionGroupCatalogContract $permissionGroupCatalog,
    ): PermissionGroup {
        return $permissionGroupCatalog->upsert(
            slug: $data->group,
            label: $data->groupLabel,
            description: $data->groupDescription,
        );
    }

    private static function restoreOrCreatePermission(
        CreatePermissionData $data,
        PermissionGroup $group,
    ): Permission {
        $permission = Permission::withTrashed()->firstOrNew([
            'name' => $data->name,
            'guard_name' => 'web',
        ]);

        $permission->forceFill([
            'name' => $data->name,
            'label' => $data->label,
            'description' => $data->description,
            'permission_group_id' => $group->id,
            'guard_name' => 'web',
            'deleted_at' => null,
        ])->save();

        return $permission;
    }

    private static function auditState(Permission $permission): array
    {
        return [
            'name' => $permission->name,
            'label' => $permission->label,
            'description' => $permission->description,
            'group' => $permission->group,
            'group_label' => $permission->group_label,
        ];
    }
}
