<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Settings\Events\PasswordUpdated;

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
