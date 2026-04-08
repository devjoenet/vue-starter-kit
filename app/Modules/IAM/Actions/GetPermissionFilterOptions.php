<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\Contracts\PermissionFilterOptionsProvider;
use App\Modules\IAM\DTOs\PermissionIndexFilterOptionsData;

final class GetPermissionFilterOptions
{
    public static function handle(PermissionFilterOptionsProvider $permissionFilterOptionsProvider): PermissionIndexFilterOptionsData
    {
        return $permissionFilterOptionsProvider->options();
    }
}
