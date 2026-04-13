<?php

declare(strict_types=1);

namespace App\Modules\IAM\Users\Actions;

use App\Modules\IAM\Roles\Actions\EnsureSuperAdminRole;
use App\Modules\IAM\Roles\Actions\HasActiveSuperAdminUser;
use App\Modules\IAM\Users\Events\UserDeleted;
use App\Modules\IAM\Users\Exceptions\CannotDeleteLastSuperAdminUser;
use App\Modules\Shared\Models\User;
use Illuminate\Support\Facades\DB;

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
