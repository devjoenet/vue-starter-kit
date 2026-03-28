<?php

declare(strict_types=1);

namespace App\Modules\Settings\Actions;

use App\Models\User;

final class UpdatePassword
{
    public static function handle(User $user, string $password): User
    {
        $user->update([
            'password' => $password,
        ]);

        return $user;
    }
}
