<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Roles\Models\Role;
use App\Modules\Users\DTOs\SyncUserRolesData;
use App\Modules\Users\Exceptions\UnknownRolesSelected;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;

final class SyncUserRoles
{
    public static function handle(User $user, SyncUserRolesData $data): User
    {
        $roleNames = self::resolveRoleNames($data);

        DB::transaction(fn (): User => tap($user, fn (User $user): User => $user->syncRoles($roleNames)));

        return $user;
    }

    /** @return list<string> */
    private static function resolveRoleNames(SyncUserRolesData $data): array
    {
        $requestedRoles = collect($data->roles)
            ->unique()
            ->values();

        $existingRoles = Role::query()
            ->whereIn('name', $requestedRoles->all())
            ->pluck('name')
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
}
