<?php

declare(strict_types=1);

namespace App\Actions\Admin\Roles;

use App\Models\Role;
use App\Support\Data\Admin\Roles\CreateRoleData;

final class CreateRole
{
    public function handle(CreateRoleData $data): Role
    {
        $role = Role::query()->create([
            'name' => $data->name,
            'guard_name' => 'web',
        ]);

        $role->users()->sync($data->user_ids);

        return $role;
    }
}
