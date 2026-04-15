<?php

declare(strict_types=1);

namespace Modules\Roles\Actions;

use Modules\Roles\Contracts\RoleFilterOptionsProvider;
use Modules\Roles\DTOs\RoleIndexFilterOptionsData;

final class GetRoleFilterOptions
{
    public static function handle(RoleFilterOptionsProvider $roleFilterOptionsProvider): RoleIndexFilterOptionsData
    {
        return $roleFilterOptionsProvider->options();
    }
}
