<?php

declare(strict_types=1);

namespace Modules\Roles\Actions;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\User;
use Modules\Roles\Models\Role;

final class HasActiveSuperAdminUser
{
    public static function handle(?User $excludingUser = null): bool
    {
        return Role::query()
            ->where('name', EnsureSuperAdminRole::name())
            ->where('guard_name', EnsureSuperAdminRole::guardName())
            ->whereHas('users', function (Builder $query) use ($excludingUser): void {
                if (! $excludingUser instanceof User) {
                    return;
                }

                $query->whereKeyNot($excludingUser->getKey());
            })
            ->exists();
    }
}
