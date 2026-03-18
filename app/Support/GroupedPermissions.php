<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Permission;
use App\Support\Data\Admin\Permissions\PermissionItemData;
use Illuminate\Support\Collection;

final class GroupedPermissions
{
    /** @return Collection<int|string, Collection<int, Permission>> */
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

    /** @return array<string, array<int, array{id: int, name: string, label: string, description: string|null, group: string, group_label: string, group_description: string|null}>> */
    public function allData(): array
    {
        return $this->all()
            ->map(fn (Collection $items): array => $items
                ->map(fn (Permission $permission): array => PermissionItemData::fromModel($permission)->all())
                ->values()
                ->all())
            ->all();
    }
}
