<?php

declare(strict_types=1);

namespace App\Actions\Admin\Roles;

use App\Models\Role;
use App\Support\Data\Admin\Roles\RoleIndexFilterOptionsData;

final class GetRoleFilterOptions
{
    public static function handle(): RoleIndexFilterOptionsData
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
