<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Roles\Contracts\RoleFilterOptionsProvider;
use App\Modules\Roles\DTOs\RoleIndexFilterOptionsData;

final class GetRoleFilterOptions
{
    public static function handle(RoleFilterOptionsProvider $roleFilterOptionsProvider): RoleIndexFilterOptionsData
    {
        return $roleFilterOptionsProvider->options();
    }
}
