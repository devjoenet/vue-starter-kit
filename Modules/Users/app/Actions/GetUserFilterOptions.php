<?php

declare(strict_types=1);

namespace Modules\Users\Actions;

use Modules\Users\Contracts\UserFilterOptionsProvider;
use Modules\Users\DTOs\UserIndexFilterOptionsData;

final class GetUserFilterOptions
{
    public static function handle(UserFilterOptionsProvider $userFilterOptionsProvider): UserIndexFilterOptionsData
    {
        return $userFilterOptionsProvider->options();
    }
}
