<?php

declare(strict_types=1);

namespace App\Actions\Admin\Roles;

use App\Models\Role;
use App\Support\Data\Admin\Roles\UpdateRoleData;

final class UpdateRole
{
    public function handle(Role $role, UpdateRoleData $data): Role
    {
        $role->forceFill([
            'name' => $data->name,
        ])->save();

        return $role;
    }
}
