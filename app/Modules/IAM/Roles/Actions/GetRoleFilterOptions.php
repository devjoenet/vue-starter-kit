<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Actions;

use App\Modules\IAM\Roles\Contracts\RoleFilterOptionsProvider;
use App\Modules\IAM\Roles\DTOs\RoleIndexFilterOptionsData;

final class GetRoleFilterOptions
{
    public static function handle(RoleFilterOptionsProvider $roleFilterOptionsProvider): RoleIndexFilterOptionsData
    {
        return $roleFilterOptionsProvider->options();
    }
}
