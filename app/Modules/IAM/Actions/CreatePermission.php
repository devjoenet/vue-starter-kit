<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\Contracts\PermissionGroupCatalogContract;
use App\Modules\IAM\DTOs\CreatePermissionData;
use App\Modules\IAM\Events\PermissionCreated;
use App\Modules\IAM\Models\Permission;
use App\Modules\IAM\Models\PermissionGroup;
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

            event(new PermissionCreated(
                $permission->loadMissing('permissionGroup'),
                $existingPermission instanceof Permission ? self::auditState($existingPermission) : null,
                $existingPermission?->trashed() === true,
            ));

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
