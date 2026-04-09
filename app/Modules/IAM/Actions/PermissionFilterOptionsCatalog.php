<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\Contracts\PermissionFilterOptionsProvider;
use App\Modules\IAM\DTOs\PermissionIndexFilterOptionsData;
use App\Modules\IAM\Models\Permission;
use App\Modules\IAM\Models\PermissionGroup;

final class PermissionFilterOptionsCatalog implements PermissionFilterOptionsProvider
{
    public function options(): PermissionIndexFilterOptionsData
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
