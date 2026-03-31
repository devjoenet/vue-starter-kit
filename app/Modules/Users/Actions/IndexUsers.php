<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Shared\Actions\GetAdminIndex;
use App\Modules\Shared\DTOs\AdminIndexQueryData;
use App\Modules\Users\Contracts\UserFilterOptionsProvider;
use Illuminate\Http\Request;

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
            'filterOptions' => GetUserFilterOptions::handle($userFilterOptionsProvider)->all(),
            'query' => AdminIndexQueryData::fromQuery($indexQuery)->all(),
        ];
    }
}
