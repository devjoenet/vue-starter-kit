<?php

declare(strict_types=1);

namespace App\Modules\IAM\Permissions\Actions;

use App\Modules\IAM\Permissions\Models\Permission;

final class CountPermissions
{
    public static function handle(): int
    {
        return Permission::query()->count();
    }
}
