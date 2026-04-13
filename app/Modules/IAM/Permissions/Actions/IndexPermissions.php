<?php

declare(strict_types=1);

namespace App\Modules\IAM\Permissions\Actions;

use App\Modules\IAM\Permissions\Contracts\PermissionFilterOptionsProvider;
use App\Modules\IAM\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\Shared\Actions\GetAdminIndex;
use App\Modules\Shared\DTOs\AdminIndexQueryData;
use Illuminate\Http\Request;

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
