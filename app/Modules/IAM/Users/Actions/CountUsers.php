<?php

declare(strict_types=1);

namespace App\Modules\IAM\Users\Actions;

use App\Modules\Shared\Models\User;

final class CountUsers
{
    public static function handle(): int
    {
        return User::query()->count();
    }
}
