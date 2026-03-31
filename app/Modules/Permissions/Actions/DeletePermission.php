<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Actions;

use App\Modules\Permissions\Models\Permission;

final class DeletePermission
{
    public static function handle(Permission $permission): void
    {
        $permission->delete();
    }
}
