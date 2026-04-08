<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\Contracts\UserFilterOptionsProvider;
use App\Modules\IAM\DTOs\UserIndexFilterOptionsData;

final class GetUserFilterOptions
{
    public static function handle(UserFilterOptionsProvider $userFilterOptionsProvider): UserIndexFilterOptionsData
    {
        return $userFilterOptionsProvider->options();
    }
}
