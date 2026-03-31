<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Actions;

use App\Modules\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\Permissions\DTOs\CreatePermissionData;
use App\Modules\Permissions\Models\Permission;

final class CreatePermission
{
    public static function handle(
        CreatePermissionData $data,
        PermissionGroupCatalogContract $permissionGroupCatalog,
    ): Permission {
        $group = $permissionGroupCatalog->upsert(
            slug: $data->group,
            label: $data->groupLabel,
            description: $data->groupDescription,
        );

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

        return $permission->load('permissionGroup');
    }
}
