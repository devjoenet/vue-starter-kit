<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Roles\Models\Role;
use App\Modules\Users\DTOs\SyncUserRolesData;
use App\Modules\Users\Exceptions\UnknownRolesSelected;
use App\Modules\Users\Models\User;

final class SyncUserRoles
{
    public static function handle(User $user, SyncUserRolesData $data): User
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

        $user->syncRoles($existingRoles->all());

        return $user;
    }
}
