<?php

declare(strict_types=1);

namespace App\Modules\Roles\Contracts;

use App\Modules\Permissions\Models\Permission;
use Illuminate\Support\Collection;

interface GroupedPermissionsProvider
{
    /** @return Collection<int|string, Collection<int, Permission>> */
    public function all(): Collection;

    /** @return array<string, array<int, array{id: int, name: string, label: string, description: string|null, group: string, group_label: string, group_description: string|null}>> */
    public function allData(): array;
}
