<?php

declare(strict_types=1);

namespace App\Actions\Admin\Users;

use App\Models\Role;
use App\Models\User;
use App\Support\Data\Admin\Users\SyncUserRolesData;
use App\Support\Exceptions\UnknownRolesSelected;

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
