<?php

declare(strict_types=1);

namespace Modules\Permissions\Actions;

use Modules\Permissions\Contracts\PermissionFilterOptionsProvider;
use Modules\Permissions\DTOs\PermissionIndexFilterOptionsData;

final class GetPermissionFilterOptions
{
    public static function handle(PermissionFilterOptionsProvider $permissionFilterOptionsProvider): PermissionIndexFilterOptionsData
    {
        return $permissionFilterOptionsProvider->options();
    }
}
