<?php

declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Models\Permission;

final class DeletePermission
{
    public function handle(Permission $permission): void
    {
        $permission->delete();
    }
}
