<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Actions;

use App\Modules\Permissions\Contracts\PermissionFilterOptionsProvider;
use App\Modules\Permissions\DTOs\PermissionIndexFilterOptionsData;

final class GetPermissionFilterOptions
{
    public static function handle(PermissionFilterOptionsProvider $permissionFilterOptionsProvider): PermissionIndexFilterOptionsData
    {
        return $permissionFilterOptionsProvider->options();
    }
}
