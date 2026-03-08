<?php

declare(strict_types=1);

namespace App\Actions\Settings;

use App\Models\User;

final class UpdatePassword
{
    public function handle(User $user, string $password): User
    {
        $user->update([
            'password' => $password,
        ]);

        return $user;
    }
}
