<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\DTOs\RoleOptionData;
use App\Modules\IAM\Models\Role;
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
