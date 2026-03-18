<?php

declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Models\Permission;
use App\Support\Data\Admin\Permissions\UpdatePermissionData;
use App\Support\PermissionGroupCatalog;

final readonly class UpdatePermission
{
    public function __construct(
        private PermissionGroupCatalog $permissionGroupCatalog,
    ) {}

    public function handle(Permission $permission, UpdatePermissionData $data): Permission
    {
        $group = $this->permissionGroupCatalog->upsert(
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
