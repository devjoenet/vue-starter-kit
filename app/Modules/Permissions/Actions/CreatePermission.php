<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Actions;

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
        $permission = DB::transaction(function () use ($data, $permissionGroupCatalog): Permission {
            $group = self::upsertGroup($data, $permissionGroupCatalog);

            return self::restoreOrCreatePermission($data, $group);
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
}
