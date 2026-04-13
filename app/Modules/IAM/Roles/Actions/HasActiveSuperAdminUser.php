<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Actions;

use App\Modules\IAM\Roles\Models\Role;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Eloquent\Builder;

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
