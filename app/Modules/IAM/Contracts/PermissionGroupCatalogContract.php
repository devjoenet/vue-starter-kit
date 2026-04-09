<?php

declare(strict_types=1);

namespace App\Modules\IAM\Contracts;

use App\Modules\IAM\DTOs\PermissionGroupOptionData;
use App\Modules\IAM\Models\PermissionGroup;
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
