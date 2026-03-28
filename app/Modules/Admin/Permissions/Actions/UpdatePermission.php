<?php

declare(strict_types=1);

namespace App\Modules\Admin\Permissions\Actions;

use App\Models\Permission;
use App\Modules\Admin\Permissions\DTOs\UpdatePermissionData;
use App\Modules\Admin\Permissions\Support\PermissionGroupCatalog;

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
