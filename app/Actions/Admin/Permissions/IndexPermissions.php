<?php

declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Actions\Admin\GetAdminIndex;
use App\Support\Data\Admin\AdminIndexQueryData;
use App\Support\PermissionGroupCatalog;
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
