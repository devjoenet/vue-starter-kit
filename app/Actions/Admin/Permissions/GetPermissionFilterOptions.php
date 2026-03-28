<?php

declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Support\Data\Admin\Permissions\PermissionIndexFilterOptionsData;
use Illuminate\Support\Collection;

final class GetPermissionFilterOptions
{
    public static function handle(Collection $permissions): PermissionIndexFilterOptionsData
    {
        return PermissionIndexFilterOptionsData::from([
            'group' => $permissions->pluck('group')->unique()->sort()->values()->all(),
            'permission' => $permissions->pluck('label')->unique()->sort()->values()->all(),
            'permission_check' => $permissions->pluck('name')->unique()->sort()->values()->all(),
        ]);
    }
}
