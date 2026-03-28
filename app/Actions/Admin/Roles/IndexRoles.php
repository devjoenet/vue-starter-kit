<?php

declare(strict_types=1);

namespace App\Actions\Admin\Roles;

use App\Actions\Admin\GetAdminIndex;
use App\Support\Data\Admin\AdminIndexQueryData;
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
