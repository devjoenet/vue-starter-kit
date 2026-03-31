<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Contracts;

use App\Modules\Permissions\Models\PermissionGroup;

interface PermissionGroupCatalogContract
{
    public function upsert(
        string $slug,
        ?string $label = null,
        ?string $description = null,
    ): PermissionGroup;

    /** @return array<int, array{slug: string, label: string, description: string|null, permissions_count: int}> */
    public function options(): array;
}
