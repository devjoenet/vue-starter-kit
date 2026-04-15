<?php

declare(strict_types=1);

namespace Modules\Users\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Roles\Actions\EnsureSuperAdminRole;
use Modules\Roles\Actions\HasActiveSuperAdminUser;
use Modules\Users\Events\UserDeleted;
use Modules\Users\Exceptions\CannotDeleteLastSuperAdminUser;

final class DeleteUser
{
    public static function handle(User $user): void
    {
        self::ensureDeletingTheUserKeepsSuperAdminAccess($user);

        DB::transaction(function () use ($user): void {
            $before = self::auditState($user);
            $user->delete();

            event(new UserDeleted($user, $before));
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

    private static function ensureDeletingTheUserKeepsSuperAdminAccess(User $user): void
    {
        if (! $user->hasRole(EnsureSuperAdminRole::name())) {
            return;
        }

        if (HasActiveSuperAdminUser::handle(excludingUser: $user)) {
            return;
        }

        throw CannotDeleteLastSuperAdminUser::forUser($user);
    }
}
