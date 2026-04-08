<?php

declare(strict_types=1);

namespace App\Modules\Settings\Actions;

use App\Modules\Settings\Events\ProfileDeleted;
use App\Modules\Shared\Models\User;
use Illuminate\Support\Facades\DB;

final class DeleteProfile
{
    public static function handle(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $before = self::auditState($user);
            $user->delete();

            event(new ProfileDeleted($user, $before));
        });
    }

    /** @return array{name: string, email: string} */
    private static function auditState(User $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
