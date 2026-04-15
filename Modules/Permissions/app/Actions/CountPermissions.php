<?php

declare(strict_types=1);

namespace Modules\Permissions\Actions;

use Modules\Permissions\Models\Permission;

final class CountPermissions
{
    public static function handle(): int
    {
        return Permission::query()->count();
    }
}
