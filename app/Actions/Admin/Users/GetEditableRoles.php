<?php

declare(strict_types=1);

namespace App\Actions\Admin\Users;

use App\Models\Role;
use App\Support\Data\Admin\Users\RoleOptionData;

final class GetEditableRoles
{
    /**
     * @return list<array{id: int, name: string}>
     */
    public static function handle(): array
    {
        return Role::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Role $role): array => RoleOptionData::fromModel($role)->all())
            ->all();
    }
}
