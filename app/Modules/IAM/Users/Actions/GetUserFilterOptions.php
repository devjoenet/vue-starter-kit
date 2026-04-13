<?php

declare(strict_types=1);

namespace App\Modules\IAM\Users\Actions;

use App\Modules\IAM\Users\Contracts\UserFilterOptionsProvider;
use App\Modules\IAM\Users\DTOs\UserIndexFilterOptionsData;

final class GetUserFilterOptions
{
    public static function handle(UserFilterOptionsProvider $userFilterOptionsProvider): UserIndexFilterOptionsData
    {
        return $userFilterOptionsProvider->options();
    }
}
