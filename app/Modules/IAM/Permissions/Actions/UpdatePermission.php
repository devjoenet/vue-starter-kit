<?php

declare(strict_types=1);

namespace App\Modules\IAM\Permissions\Actions;

use App\Modules\IAM\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\IAM\Permissions\DTOs\UpdatePermissionData;
use App\Modules\IAM\Permissions\Events\PermissionUpdated;
use App\Modules\IAM\Permissions\Models\Permission;
use App\Modules\IAM\Permissions\Models\PermissionGroup;
use Illuminate\Support\Facades\DB;

final class UpdatePermission
{
    public static function handle(
        Permission $permission,
        UpdatePermissionData $data,
        PermissionGroupCatalogContract $permissionGroupCatalog,
    ): void {
        $before = self::auditState($permission->loadMissing('permissionGroup'));
        DB::transaction(function () use ($permission, $data, $permissionGroupCatalog, $before): void {
            $group = self::upsertGroup($data, $permissionGroupCatalog);
            $permission = self::persistPermission($permission, $data, $group);

            event(new PermissionUpdated($permission->load('permissionGroup'), $before));
        });
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
