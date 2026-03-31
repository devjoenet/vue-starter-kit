<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Users\Models\User;

final class DeleteUser
{
    public static function handle(User $user): void
    {
        $user->delete();
    }
}
