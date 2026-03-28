<?php

declare(strict_types=1);

namespace App\Modules\Settings\Actions;

use App\Models\User;

final class DeleteProfile
{
    public static function handle(User $user): void
    {
        $user->delete();
    }
}
