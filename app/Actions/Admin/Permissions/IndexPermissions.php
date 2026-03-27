<?php declare(strict_types=1);

namespace App\Actions\Admin\Permissions;

use App\Actions\Admin\GetAdminIndex;
use App\Support\Data\Admin\AdminIndexQueryData;
use App\Actions\Admin\Permissions\GetPermissionIndexItems;
use App\Support\PermissionGroupCatalog;
use Illuminate\Http\Request;

class IndexPermissions
{
    public static function handle(Request $request, PermissionGroupCatalog $permissionGroupCatalog): array
    {
        $indexQuery = GetAdminIndex::query($request);
        $permissions = GetPermissionIndexItems::handle();
        $filteredPermissions = FilterPermissions::handle($permissions, $indexQuery);
        $sortedPermissions = SortPermissions::handle($filteredPermissions, $indexQuery);

        return [
            'permissions' => $sortedPermissions->values()->all(),
            'groups' => $permissionGroupCatalog->options(),
            'filterOptions' => GetPermissionFilterOptions::handle($permissions)->all(),
            'query' => AdminIndexQueryData::fromQuery($indexQuery)->all(),
        ];
    }
}
