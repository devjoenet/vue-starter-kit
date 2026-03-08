<?php

declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Models\Permission;
use App\Support\Data\Admin\Permissions\CreatePermissionData;

final class CreatePermission
{
    public function handle(CreatePermissionData $data): Permission
    {
        return Permission::query()->create([
            'name' => $data->name,
            'group' => $data->group,
            'guard_name' => 'web',
        ]);
    }
}
