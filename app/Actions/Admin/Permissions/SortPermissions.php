<?php

declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Support\AdminIndexQuery;
use Illuminate\Support\Collection;

class SortPermissions
{
    public static function handle(Collection $permissions, AdminIndexQuery $indexQuery): Collection
    {
        return match ($indexQuery->sort) {
            'group' => $permissions->sortBy(
                fn (array $permission): string => sprintf('%s|%s', $permission['group_label'], $permission['group']),
                SORT_NATURAL,
                $indexQuery->direction === 'desc',
            ),
            'permission' => $permissions->sortBy('label', SORT_NATURAL, $indexQuery->direction === 'desc'),
            'permission_check' => $permissions->sortBy('name', SORT_NATURAL, $indexQuery->direction === 'desc'),
            default => $permissions->sortBy('id', SORT_NUMERIC, $indexQuery->direction === 'desc'),
        };
    }
}
