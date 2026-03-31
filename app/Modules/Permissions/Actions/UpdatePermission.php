<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Actions;

use App\Modules\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\Permissions\DTOs\UpdatePermissionData;
use App\Modules\Permissions\Models\Permission;

final class UpdatePermission
{
    public static function handle(
        Permission $permission,
        UpdatePermissionData $data,
        PermissionGroupCatalogContract $permissionGroupCatalog,
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
