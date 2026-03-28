<?php

declare(strict_types=1);

namespace App\Modules\Admin\Permissions\Queries;

use App\Modules\Admin\Permissions\Support\PermissionGroupCatalog;
use App\Modules\Admin\Shared\DTOs\AdminIndexQueryData;
use App\Modules\Admin\Shared\Queries\GetAdminIndex;
use Illuminate\Http\Request;

final class IndexPermissions
{
    public static function handle(Request $request, PermissionGroupCatalog $permissionGroupCatalog): array
    {
        $indexQuery = GetAdminIndex::handle(
            request: $request,
            allowedSorts: ['id', 'group', 'permission', 'permission_check'],
            allowedFilters: ['group', 'permission', 'permission_check'],
        );

        return [
            'permissions' => GetPermissionIndexItems::handle($indexQuery),
            'groups' => $permissionGroupCatalog->options(),
            'filterOptions' => GetPermissionFilterOptions::handle()->all(),
            'query' => AdminIndexQueryData::fromQuery($indexQuery)->all(),
        ];
    }
}
