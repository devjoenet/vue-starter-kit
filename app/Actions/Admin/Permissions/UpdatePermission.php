<?php

declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Models\Permission;
use App\Support\Data\Admin\Permissions\UpdatePermissionData;

final class UpdatePermission
{
    public function handle(Permission $permission, UpdatePermissionData $data): Permission
    {
        $permission->forceFill([
            'name' => $data->name,
            'group' => $data->group,
        ])->save();

        return $permission;
    }
}
