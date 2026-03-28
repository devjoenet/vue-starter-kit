<?php

declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Models\Permission;
use App\Support\Data\Admin\Permissions\UpdatePermissionData;
use App\Support\PermissionGroupCatalog;

final class UpdatePermission
{
    public static function handle(
        Permission $permission,
        UpdatePermissionData $data,
        PermissionGroupCatalog $permissionGroupCatalog,
    ): Permission {
        $group = $permissionGroupCatalog->upsert(
            slug: $data->group,
            label: $data->groupLabel,
            description: $data->groupDescription,
        );

        $permission->forceFill([
            'label' => $data->label,
            'description' => $data->description,
            'permission_group_id' => $group->id,
        ])->save();

        return $permission->load('permissionGroup');
    }
}
