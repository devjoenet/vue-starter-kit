<?php

declare(strict_types=1);

namespace Modules\Users\Actions;

use Modules\Core\Models\User;

final class CountUsers
{
    public static function handle(): int
    {
        return User::query()->count();
    }
}
