<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\Contracts\GroupedPermissionsProvider;
use App\Modules\IAM\DTOs\PermissionItemData;
use App\Modules\IAM\Models\Permission;
use Illuminate\Support\Collection;

final class GroupedPermissions implements GroupedPermissionsProvider
{
    /** @return Collection<string, Collection<int, Permission>> */
    public function all(): Collection
    {
        return Permission::query()
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
                fn (Collection $items): Collection => collect($items->all())->values(),
            );
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
