<?php declare(strict_types=1);

namespace App\Actions\Admin;

use App\Support\AdminIndexQuery;
use App\Support\Data\Admin\AdminIndexQueryData;
use Illuminate\Http\Request;

class GetAdminIndex
{
    /** Generates an `AdminIndexQuery` from a given `Request` */
    public static function query(Request $request): AdminIndexQuery
    {
        return AdminIndexQuery::fromRequest(
            request: $request,
            allowedSorts: ['id', 'group', 'permission', 'permission_check'],
            allowedFilters: ['group', 'permission', 'permission_check'],
        );
    }

    /** Generates an `AdminIndexQueryData` object from a given `AdminIndexQuery` */
    public static function data(AdminIndexQuery $adminIndexQuery): AdminIndexQueryData
    {
        return AdminIndexQueryData::fromQuery($adminIndexQuery);
    }
}
