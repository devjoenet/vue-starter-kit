<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Actions;

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
        $permission = DB::transaction(function () use ($permission, $data, $permissionGroupCatalog): Permission {
            $group = self::upsertGroup($data, $permissionGroupCatalog);

            return self::persistPermission($permission, $data, $group);
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
}
