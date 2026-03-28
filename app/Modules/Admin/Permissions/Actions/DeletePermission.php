<?php

declare(strict_types=1);

namespace App\Modules\Admin\Permissions\Actions;

use App\Models\Permission;

final class DeletePermission
{
    public static function handle(Permission $permission): void
    {
        $permission->delete();
    }
}
