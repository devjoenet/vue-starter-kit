<?php

declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Models\Permission;
use App\Support\Data\Admin\Permissions\CreatePermissionData;
use App\Support\PermissionGroupCatalog;

final class CreatePermission
{
    public function __construct(
        private readonly PermissionGroupCatalog $permissionGroupCatalog,
    ) {}

    public function handle(CreatePermissionData $data): Permission
    {
        $group = $this->permissionGroupCatalog->upsert(
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
