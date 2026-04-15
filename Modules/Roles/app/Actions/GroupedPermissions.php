<?php

declare(strict_types=1);

namespace Modules\Roles\Actions;

use Illuminate\Support\Collection;
use Modules\Permissions\DTOs\PermissionItemData;
use Modules\Permissions\Models\Permission;
use Modules\Roles\Contracts\GroupedPermissionsProvider;

final class GroupedPermissions implements GroupedPermissionsProvider
{
    /** @return Collection<string, Collection<int, Permission>> */
    public function all(): Collection
    {
        /** @var Collection<string, Collection<int, Permission>> $permissions */
        $permissions = Permission::query()
            ->with('permissionGroup')
            ->select(['id', 'permission_group_id', 'name', 'label', 'description'])
            ->get()
            ->sortBy([
                fn (Permission $permission): string => $permission->group_label,
                fn (Permission $permission): string => $permission->display_label,
                fn (Permission $permission): string => $permission->name,
            ])
            ->groupBy(fn (Permission $permission): string => $permission->group)
            ->map(
                /**
                 * @param  Collection<int, Permission>  $items
                 * @return Collection<int, Permission>
                 */
                fn (Collection $items): Collection => $items->values(),
            );

        return $permissions;
    }

    /** @return Collection<string, Collection<int, PermissionItemData>> */
    public function allData(): Collection
    {
        return $this->all()
            ->map(function (Collection $items): Collection {
                /** @var Collection<int, PermissionItemData> $permissions */
                $permissions = PermissionItemData::collect($items, Collection::class);

                return $permissions->values();
            });
    }
}
