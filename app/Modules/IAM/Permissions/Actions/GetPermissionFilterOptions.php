<?php

declare(strict_types=1);

namespace App\Modules\IAM\Permissions\Actions;

use App\Modules\IAM\Permissions\Contracts\PermissionFilterOptionsProvider;
use App\Modules\IAM\Permissions\DTOs\PermissionIndexFilterOptionsData;

final class GetPermissionFilterOptions
{
    public static function handle(PermissionFilterOptionsProvider $permissionFilterOptionsProvider): PermissionIndexFilterOptionsData
    {
        return $permissionFilterOptionsProvider->options();
    }
}
