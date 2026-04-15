<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Settings\Events\ProfileDeleted;

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
