<?php

declare(strict_types=1);

namespace App\Modules\Admin\Roles\Queries;

use App\Modules\Admin\Shared\DTOs\AdminIndexQueryData;
use App\Modules\Admin\Shared\Queries\GetAdminIndex;
use Illuminate\Http\Request;

final class IndexRoles
{
    public static function handle(Request $request): array
    {
        $indexQuery = GetAdminIndex::handle(
            request: $request,
            allowedSorts: ['id', 'display_name', 'slug', 'users'],
            allowedFilters: ['display_name', 'slug', 'users'],
        );

        return [
            'roles' => GetRoleIndexItems::handle($indexQuery),
            'filterOptions' => GetRoleFilterOptions::handle()->all(),
            'query' => AdminIndexQueryData::fromQuery($indexQuery)->all(),
        ];
    }
}
