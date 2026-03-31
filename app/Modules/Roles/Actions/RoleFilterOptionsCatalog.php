<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Roles\Contracts\RoleFilterOptionsProvider;
use App\Modules\Roles\DTOs\RoleIndexFilterOptionsData;
use App\Modules\Roles\Models\Role;

final class RoleFilterOptionsCatalog implements RoleFilterOptionsProvider
{
    public function options(): RoleIndexFilterOptionsData
    {
        $roleNames = Role::query()
            ->select('name')
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $userCounts = Role::query()
            ->withCount('users')
            ->get()
            ->pluck('users_count')
            ->map(fn (int $count): string => (string) $count)
            ->unique()
            ->sort()
            ->values()
            ->all();

        return RoleIndexFilterOptionsData::from([
            'display_name' => $roleNames,
            'slug' => $roleNames,
            'users' => $userCounts,
        ]);
    }
}
