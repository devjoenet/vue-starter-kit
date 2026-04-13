<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Actions;

use App\Modules\IAM\Roles\Contracts\RoleFilterOptionsProvider;
use App\Modules\IAM\Roles\DTOs\RoleIndexFilterOptionsData;
use App\Modules\IAM\Roles\Models\Role;

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
