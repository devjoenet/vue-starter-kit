<?php

declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Http\Requests\Admin\StorePermissionRequest;
use App\Models\Permission;
use App\Support\Data\Admin\Permissions\CreatePermissionData;
use App\Support\PermissionGroupCatalog;

final readonly class StorePermission
{
    public static function handle(StorePermissionRequest $request): Permission
    {
        $data = new CreatePermissionData(
            name: (string) $request->validated('name'),
            label: (string) $request->validated('label'),
            description: $request->validated('description'),
            group: (string) $request->validated('group'),
            groupLabel: (string) $request->validated('group_label'),
            groupDescription: $request->validated('group_description'),
        );

        $group = new PermissionGroupCatalog()->upsert(
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
