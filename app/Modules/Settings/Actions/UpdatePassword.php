<?php

declare(strict_types=1);

namespace App\Modules\Settings\Actions;

use App\Modules\Settings\Events\PasswordUpdated;
use App\Modules\Shared\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdatePassword
{
    public static function handle(User $user, string $password): void
    {
        DB::transaction(function () use ($user, $password): void {
            $user->update(['password' => $password]);

            event(new PasswordUpdated($user));
        });
    }
}
