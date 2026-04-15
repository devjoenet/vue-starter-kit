<?php

declare(strict_types=1);

namespace Modules\Users\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Roles\Actions\EnsureSuperAdminRole;
use Modules\Roles\Actions\HasActiveSuperAdminUser;
use Modules\Roles\Exceptions\CannotRemoveLastSuperAdminRoleAssignment;
use Modules\Roles\Exceptions\UnknownRolesSelected;
use Modules\Roles\Models\Role;
use Modules\Users\DTOs\SyncUserRolesData;
use Modules\Users\Events\UserRolesSynced;

final class SyncUserRoles
{
    public static function handle(User $user, SyncUserRolesData $data): void
    {
        $roleNames = self::resolveRoleNames($data);
        self::ensureRoleSyncKeepsSuperAdminAccess($user, $roleNames);
        $before = $user->getRoleNames()->sort()->values()->all();

        DB::transaction(function () use ($user, $roleNames, $before): void {
            $user->syncRoles($roleNames);

            event(new UserRolesSynced($user, $before, $roleNames));
        });
    }

    /** @return list<string> */
    private static function resolveRoleNames(SyncUserRolesData $data): array
    {
        $requestedRoles = collect($data->roleNames());

        $existingRoles = Role::query()
            ->whereIn('name', $requestedRoles->all())
            ->pluck('name')
            ->sort()
            ->values();

        $missingRoles = $requestedRoles
            ->diff($existingRoles)
            ->values()
            ->all();

        if ($missingRoles !== []) {
            throw UnknownRolesSelected::fromNames($missingRoles);
        }

        /** @var list<string> $roleNames */
        $roleNames = $existingRoles->all();

        return $roleNames;
    }

    /** @param  list<string>  $roleNames */
    private static function ensureRoleSyncKeepsSuperAdminAccess(User $user, array $roleNames): void
    {
        if (! $user->hasRole(EnsureSuperAdminRole::name())) {
            return;
        }

        if (in_array(EnsureSuperAdminRole::name(), $roleNames, true)) {
            return;
        }

        if (HasActiveSuperAdminUser::handle(excludingUser: $user)) {
            return;
        }

        throw CannotRemoveLastSuperAdminRoleAssignment::forUser($user, $roleNames);
    }
}
