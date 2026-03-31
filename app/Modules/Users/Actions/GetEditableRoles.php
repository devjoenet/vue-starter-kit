<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Roles\Models\Role;
use App\Modules\Users\DTOs\RoleOptionData;

final class GetEditableRoles
{
    /** @return list<array{id: int, name: string}> */
    public static function handle(): array
    {
        return Role::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Role $role): array => RoleOptionData::fromModel($role)->all())
            ->all();
    }
}
