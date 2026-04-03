<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\Permissions\DTOs\UpdatePermissionData;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\PermissionGroup;
use Illuminate\Support\Facades\DB;

final class UpdatePermission
{
    public static function handle(
        Permission $permission,
        UpdatePermissionData $data,
        PermissionGroupCatalogContract $permissionGroupCatalog,
    ): Permission {
        $before = self::auditState($permission->loadMissing('permissionGroup'));
        $permission = DB::transaction(function () use ($permission, $data, $permissionGroupCatalog, $before): Permission {
            $group = self::upsertGroup($data, $permissionGroupCatalog);
            $permission = self::persistPermission($permission, $data, $group);

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: 'permissions.updated',
                summary: sprintf('Updated permission %s.', $permission->name),
                subjectType: Permission::class,
                subjectId: (int) $permission->getKey(),
                subjectLabel: $permission->name,
                changes: [
                    'before' => $before,
                    'after' => self::auditState($permission->load('permissionGroup')),
                ],
            )));

            return $permission;
        });

        return $permission->load('permissionGroup');
    }

    private static function upsertGroup(
        UpdatePermissionData $data,
        PermissionGroupCatalogContract $permissionGroupCatalog,
    ): PermissionGroup {
        return $permissionGroupCatalog->upsert(
            slug: $data->group,
            label: $data->groupLabel,
            description: $data->groupDescription,
        );
    }

    private static function persistPermission(
        Permission $permission,
        UpdatePermissionData $data,
        PermissionGroup $group,
    ): Permission {
        $permission->forceFill([
            'label' => $data->label,
            'description' => $data->description,
            'permission_group_id' => $group->id,
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
