<?php

declare(strict_types=1);

namespace App\Actions\Admin\Roles;

use App\Models\Role;

final class DeleteRole
{
    public function handle(Role $role): void
    {
        $role->delete();
    }
}
