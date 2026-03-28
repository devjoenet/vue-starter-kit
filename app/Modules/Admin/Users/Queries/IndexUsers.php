<?php

declare(strict_types=1);

namespace App\Modules\Admin\Users\Queries;

use App\Modules\Admin\Shared\DTOs\AdminIndexQueryData;
use App\Modules\Admin\Shared\Queries\GetAdminIndex;
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
