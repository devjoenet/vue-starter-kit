<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\Contracts\UserFilterOptionsProvider;
use App\Modules\Shared\Actions\GetAdminIndex;
use App\Modules\Shared\DTOs\AdminIndexQueryData;
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
            'filterOptions' => GetUserFilterOptions::handle($userFilterOptionsProvider),
            'query' => AdminIndexQueryData::fromQuery($indexQuery),
        ];
    }
}
