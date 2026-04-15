<?php

declare(strict_types=1);

namespace Modules\Permissions\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Permissions\Contracts\PermissionGroupCatalogContract;
use Modules\Permissions\DTOs\PermissionGroupOptionData;
use Modules\Permissions\Models\PermissionGroup;

final class PermissionGroupCatalog implements PermissionGroupCatalogContract
{
    public function upsert(
        string $slug,
        ?string $label = null,
        ?string $description = null,
    ): PermissionGroup {
        $group = PermissionGroup::withTrashed()->firstOrNew([
            'slug' => $slug,
        ]);

        $group->forceFill([
            'label' => $label ?: $this->defaultLabel($slug),
            'description' => $description,
            'deleted_at' => null,
        ])->save();

        return $group;
    }

    /** @return Collection<int, PermissionGroupOptionData> */
    public function options(): Collection
    {
        /** @var Collection<int, PermissionGroupOptionData> $groups */
        $groups = PermissionGroupOptionData::collect(
            PermissionGroup::query()
                ->withCount('permissions')
                ->orderBy('label')
                ->orderBy('slug')
                ->get(),
            Collection::class,
        );

        return $groups;
    }

    private function defaultLabel(string $slug): string
    {
        return Str::of($slug)
            ->replace('_', ' ')
            ->title()
            ->toString();
    }
}
