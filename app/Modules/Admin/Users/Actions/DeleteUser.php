<?php

declare(strict_types=1);

namespace App\Modules\Admin\Users\Actions;

use App\Models\User;

final class DeleteUser
{
    public static function handle(User $user): void
    {
        $user->delete();
    }
}
