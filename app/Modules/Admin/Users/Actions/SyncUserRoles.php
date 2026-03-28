<?php

declare(strict_types=1);

namespace App\Modules\Admin\Users\Actions;

use App\Models\Role;
use App\Models\User;
use App\Modules\Admin\Users\DTOs\SyncUserRolesData;
use App\Modules\Admin\Users\Exceptions\UnknownRolesSelected;

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
