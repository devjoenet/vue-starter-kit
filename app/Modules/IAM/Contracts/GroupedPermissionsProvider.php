<?php

declare(strict_types=1);

namespace App\Modules\IAM\Contracts;

use App\Modules\IAM\DTOs\PermissionItemData;
use App\Modules\IAM\Models\Permission;
use Illuminate\Support\Collection;

interface GroupedPermissionsProvider
{
    /** @return Collection<string, Collection<int, Permission>> */
    public function all(): Collection;

    /** @return Collection<string, Collection<int, PermissionItemData>> */
    public function allData(): Collection;
}
