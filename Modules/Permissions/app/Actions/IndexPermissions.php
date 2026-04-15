<?php

declare(strict_types=1);

namespace Modules\Permissions\Actions;

use Illuminate\Http\Request;
use Modules\Core\Actions\GetAdminIndex;
use Modules\Core\DTOs\AdminIndexQueryData;
use Modules\Permissions\Contracts\PermissionFilterOptionsProvider;
use Modules\Permissions\Contracts\PermissionGroupCatalogContract;

final class IndexPermissions
{
    public static function handle(
        Request $request,
        PermissionGroupCatalogContract $permissionGroupCatalog,
        PermissionFilterOptionsProvider $permissionFilterOptionsProvider,
    ): array {
        $indexQuery = GetAdminIndex::handle(
            request: $request,
            allowedSorts: ['id', 'group', 'permission', 'permission_check'],
            allowedFilters: ['group', 'permission', 'permission_check'],
        );

        return [
            'permissions' => GetPermissionIndexItems::handle($indexQuery),
            'groups' => $permissionGroupCatalog->options(),
            'filterOptions' => GetPermissionFilterOptions::handle($permissionFilterOptionsProvider),
            'query' => AdminIndexQueryData::fromQuery($indexQuery),
        ];
    }
}
