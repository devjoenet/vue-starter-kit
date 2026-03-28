<?php

declare(strict_types=1);

namespace App\Modules\Admin\Permissions\Queries;

use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Modules\Admin\Permissions\DTOs\PermissionIndexFilterOptionsData;

final class GetPermissionFilterOptions
{
    public static function handle(): PermissionIndexFilterOptionsData
    {
        return PermissionIndexFilterOptionsData::from([
            'group' => PermissionGroup::query()
                ->whereHas('permissions')
                ->orderBy('label')
                ->orderBy('slug')
                ->pluck('slug')
                ->all(),
            'permission' => Permission::query()
                ->select('label')
                ->whereNotNull('label')
                ->distinct()
                ->orderBy('label')
                ->pluck('label')
                ->all(),
            'permission_check' => Permission::query()
                ->select('name')
                ->orderBy('name')
                ->pluck('name')
                ->all(),
        ]);
    }
}
