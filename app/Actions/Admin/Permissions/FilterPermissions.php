<?php

declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Support\AdminIndexQuery;
use Illuminate\Support\Collection;

class FilterPermissions
{
    public static function handle(Collection $permissions, AdminIndexQuery $indexQuery): Collection
    {
        $groupFilters = $indexQuery->filterValues('group');
        $permissionFilters = $indexQuery->filterValues('permission');
        $permissionCheckFilters = $indexQuery->filterValues('permission_check');

        if ($groupFilters !== []) {
            $permissions = $permissions->whereIn('group', $groupFilters);
        }

        if ($permissionFilters !== []) {
            $permissions = $permissions->whereIn('label', $permissionFilters);
        }

        if ($permissionCheckFilters === []) {
            return $permissions;
        }

        return $permissions->whereIn('name', $permissionCheckFilters);
    }
}
