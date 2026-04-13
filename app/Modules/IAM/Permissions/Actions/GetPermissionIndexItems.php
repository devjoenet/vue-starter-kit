<?php

declare(strict_types=1);

namespace App\Modules\IAM\Permissions\Actions;

use App\Modules\IAM\Permissions\DTOs\PermissionIndexItemData;
use App\Modules\IAM\Permissions\Models\Permission;
use App\Modules\Shared\Actions\AdminIndexQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class GetPermissionIndexItems
{
    /** @return Collection<int, PermissionIndexItemData> */
    public static function handle(AdminIndexQuery $indexQuery): Collection
    {
        /** @var Collection<int, PermissionIndexItemData> $permissions */
        $permissions = PermissionIndexItemData::collect(
            self::query($indexQuery)->get(),
            Collection::class,
        );

        return $permissions;
    }

    /** @return Builder<Permission> */
    private static function query(AdminIndexQuery $indexQuery): Builder
    {
        $query = Permission::query()
            ->select([
                'permissions.id',
                'permissions.permission_group_id',
                'permissions.name',
                'permissions.label',
                'permissions.description',
            ])
            ->with('permissionGroup')
            ->leftJoin('permission_groups', 'permission_groups.id', '=', 'permissions.permission_group_id');

        self::applyFilters($query, $indexQuery);
        self::applySorting($query, $indexQuery);

        return $query;
    }

    /** @param  Builder<Permission>  $query */
    private static function applyFilters(Builder $query, AdminIndexQuery $indexQuery): void
    {
        $groupFilters = $indexQuery->filterValues('group');
        $permissionFilters = $indexQuery->filterValues('permission');
        $permissionCheckFilters = $indexQuery->filterValues('permission_check');

        if ($groupFilters !== []) {
            $query->whereIn('permission_groups.slug', $groupFilters);
        }

        if ($permissionFilters !== []) {
            $query->whereIn('permissions.label', $permissionFilters);
        }

        if ($permissionCheckFilters === []) {
            return;
        }

        $query->whereIn('permissions.name', $permissionCheckFilters);
    }

    /** @param  Builder<Permission>  $query */
    private static function applySorting(Builder $query, AdminIndexQuery $indexQuery): void
    {
        match ($indexQuery->sort) {
            'group' => $query
                ->orderBy('permission_groups.label', $indexQuery->direction)
                ->orderBy('permission_groups.slug', $indexQuery->direction)
                ->orderBy('permissions.id'),
            'permission' => $query
                ->orderBy('permissions.label', $indexQuery->direction)
                ->orderBy('permissions.id'),
            'permission_check' => $query
                ->orderBy('permissions.name', $indexQuery->direction)
                ->orderBy('permissions.id'),
            default => $query->orderBy('permissions.id', $indexQuery->direction),
        };
    }
}
