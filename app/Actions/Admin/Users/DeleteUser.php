<?php

declare(strict_types=1);

namespace App\Actions\Admin\Users;

use App\Models\User;

final class DeleteUser
{
    public static function handle(User $user): void
    {
        $user->delete();
    }
}
