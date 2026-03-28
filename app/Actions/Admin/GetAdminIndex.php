<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Support\AdminIndexQuery;
use Illuminate\Http\Request;

final class GetAdminIndex
{
    public static function handle(Request $request): AdminIndexQuery
    {
        return AdminIndexQuery::fromRequest(
            request: $request,
            allowedSorts: ['id', 'group', 'permission', 'permission_check'],
            allowedFilters: ['group', 'permission', 'permission_check'],
        );
    }
}
