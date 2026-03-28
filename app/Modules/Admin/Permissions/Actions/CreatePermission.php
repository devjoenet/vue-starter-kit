<?php

declare(strict_types=1);

namespace App\Modules\Admin\Permissions\Actions;

use App\Models\Permission;
use App\Modules\Admin\Permissions\DTOs\CreatePermissionData;
use App\Modules\Admin\Permissions\Support\PermissionGroupCatalog;

final class CreatePermission
{
    public static function handle(
        CreatePermissionData $data,
        PermissionGroupCatalog $permissionGroupCatalog,
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
