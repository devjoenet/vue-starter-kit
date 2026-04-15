<?php

declare(strict_types=1);

namespace Modules\Users\Actions;

use Illuminate\Http\Request;
use Modules\Core\Actions\GetAdminIndex;
use Modules\Core\DTOs\AdminIndexQueryData;
use Modules\Users\Contracts\UserFilterOptionsProvider;

final class IndexUsers
{
    public static function handle(Request $request, UserFilterOptionsProvider $userFilterOptionsProvider): array
    {
        $indexQuery = GetAdminIndex::handle(
            request: $request,
            allowedSorts: ['id', 'name', 'email', 'roles'],
            allowedFilters: ['name', 'email', 'roles'],
        );

        return [
            'users' => GetUserIndexItems::handle($indexQuery),
            'filterOptions' => GetUserFilterOptions::handle($userFilterOptionsProvider),
            'query' => AdminIndexQueryData::fromQuery($indexQuery),
        ];
    }
}
