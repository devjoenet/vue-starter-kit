<?php

declare(strict_types=1);

namespace App\Modules\IAM\Permissions\Contracts;

use App\Modules\IAM\Permissions\DTOs\PermissionGroupOptionData;
use App\Modules\IAM\Permissions\Models\PermissionGroup;
use Illuminate\Support\Collection;

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
