<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\Contracts\RoleFilterOptionsProvider;
use App\Modules\IAM\DTOs\RoleIndexFilterOptionsData;

final class GetRoleFilterOptions
{
    public static function handle(RoleFilterOptionsProvider $roleFilterOptionsProvider): RoleIndexFilterOptionsData
    {
        return $roleFilterOptionsProvider->options();
    }
}
