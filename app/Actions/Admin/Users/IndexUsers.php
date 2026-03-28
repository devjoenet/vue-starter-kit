<?php

declare(strict_types=1);

namespace App\Actions\Admin\Users;

use App\Actions\Admin\GetAdminIndex;
use App\Support\Data\Admin\AdminIndexQueryData;
use Illuminate\Http\Request;

final class IndexUsers
{
    public static function handle(Request $request): array
    {
        $indexQuery = GetAdminIndex::handle(
            request: $request,
            allowedSorts: ['id', 'name', 'email', 'roles'],
            allowedFilters: ['name', 'email', 'roles'],
        );

        return [
            'users' => GetUserIndexItems::handle($indexQuery),
            'filterOptions' => GetUserFilterOptions::handle()->all(),
            'query' => AdminIndexQueryData::fromQuery($indexQuery)->all(),
        ];
    }
}
