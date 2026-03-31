<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Actions;

use App\Modules\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\Permissions\DTOs\PermissionGroupOptionData;
use App\Modules\Permissions\Models\PermissionGroup;
use Illuminate\Support\Str;

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

    /** @return array<int, array{slug: string, label: string, description: string|null, permissions_count: int}> */
    public function options(): array
    {
        return PermissionGroup::query()
            ->withCount('permissions')
            ->orderBy('label')
            ->orderBy('slug')
            ->get()
            ->map(fn (PermissionGroup $group): array => PermissionGroupOptionData::fromModel($group)->all())
            ->values()
            ->all();
    }

    private function defaultLabel(string $slug): string
    {
        return Str::of($slug)
            ->replace('_', ' ')
            ->title()
            ->toString();
    }
}
