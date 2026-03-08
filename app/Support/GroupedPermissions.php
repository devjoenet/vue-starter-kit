<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Permission;
use App\Support\Data\Admin\Permissions\PermissionItemData;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

final class GroupedPermissions
{
    /** @return Collection<int|string, EloquentCollection<int, Permission>> */
    public function all(): Collection
    {
        return Permission::query()
            ->select(['id', 'name', 'group'])
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group')
            ->map(fn (Collection $items) => $items->values());
    }

    /** @return array<string, array<int, array{id: int, name: string, group: string}>> */
    public function allData(): array
    {
        return $this->all()
            ->map(fn (Collection $items): array => $items
                ->map(fn (Permission $permission): array => PermissionItemData::fromModel($permission)->all())
                ->values()
                ->all())
            ->all();
    }

    /** @return array<int, string> */
    public function groups(): array
    {
        return Permission::query()
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->filter()
            ->values()
            ->all();
    }
}
