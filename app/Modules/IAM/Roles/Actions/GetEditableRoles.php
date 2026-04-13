<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Actions;

use App\Modules\IAM\Roles\DTOs\RoleOptionData;
use App\Modules\IAM\Roles\Models\Role;
use Illuminate\Support\Collection;

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
