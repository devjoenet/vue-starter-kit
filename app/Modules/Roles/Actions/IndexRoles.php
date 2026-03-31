<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Roles\Contracts\RoleFilterOptionsProvider;
use App\Modules\Shared\Actions\GetAdminIndex;
use App\Modules\Shared\DTOs\AdminIndexQueryData;
use Illuminate\Http\Request;

final class IndexRoles
{
    public static function handle(Request $request, RoleFilterOptionsProvider $roleFilterOptionsProvider): array
    {
        $indexQuery = GetAdminIndex::handle(
            request: $request,
            allowedSorts: ['id', 'display_name', 'slug', 'users'],
            allowedFilters: ['display_name', 'slug', 'users'],
        );

        return [
            'roles' => GetRoleIndexItems::handle($indexQuery),
            'filterOptions' => GetRoleFilterOptions::handle($roleFilterOptionsProvider)->all(),
            'query' => AdminIndexQueryData::fromQuery($indexQuery)->all(),
        ];
    }
}
