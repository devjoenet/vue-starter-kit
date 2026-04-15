<?php

declare(strict_types=1);

namespace Modules\Roles\Actions;

use Illuminate\Support\Collection;
use Modules\Roles\DTOs\RoleOptionData;
use Modules\Roles\Models\Role;

final class GetEditableRoles
{
    /** @return Collection<int, RoleOptionData> */
    public static function handle(): Collection
    {
        /** @var Collection<int, RoleOptionData> $roles */
        $roles = RoleOptionData::collect(
            Role::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            Collection::class,
        );

        return $roles;
    }
}
