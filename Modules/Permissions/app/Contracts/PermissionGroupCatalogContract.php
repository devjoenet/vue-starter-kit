<?php

declare(strict_types=1);

namespace Modules\Permissions\Contracts;

use Illuminate\Support\Collection;
use Modules\Permissions\DTOs\PermissionGroupOptionData;
use Modules\Permissions\Models\PermissionGroup;

interface PermissionGroupCatalogContract
{
    public function upsert(
        string $slug,
        ?string $label = null,
        ?string $description = null,
    ): PermissionGroup;

    /** @return Collection<int, PermissionGroupOptionData> */
    public function options(): Collection;
}
