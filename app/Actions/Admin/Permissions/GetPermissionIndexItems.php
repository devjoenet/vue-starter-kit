<?php

declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Models\Permission;
use App\Support\Data\Admin\Permissions\PermissionIndexItemData;
use Illuminate\Support\Collection;

final class GetPermissionIndexItems
{
    /** @return Collection<PermissionIndexItemData> */
    public static function handle(): Collection
    {
        return Permission::query()
            ->with('permissionGroup')
            ->select([
                'id',
                'permission_group_id',
                'name',
                'label',
                'description',
            ])
            ->get()
            ->map(fn (Permission $permission): PermissionIndexItemData => PermissionIndexItemData::fromModel($permission));
    }
}
