<?php

declare(strict_types=1);

namespace Modules\Roles\Contracts;

use Illuminate\Support\Collection;
use Modules\Permissions\DTOs\PermissionItemData;
use Modules\Permissions\Models\Permission;

interface GroupedPermissionsProvider
{
    /** @return Collection<string, Collection<int, Permission>> */
    public function all(): Collection;

    /** @return Collection<string, Collection<int, PermissionItemData>> */
    public function allData(): Collection;
}
