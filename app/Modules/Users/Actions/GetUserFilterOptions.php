<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Users\Contracts\UserFilterOptionsProvider;
use App\Modules\Users\DTOs\UserIndexFilterOptionsData;

final class GetUserFilterOptions
{
    public static function handle(UserFilterOptionsProvider $userFilterOptionsProvider): UserIndexFilterOptionsData
    {
        return $userFilterOptionsProvider->options();
    }
}
