<?php

declare(strict_types=1);

namespace App\Modules\Admin\Shared\Queries;

use App\Modules\Admin\Shared\Support\AdminIndexQuery;
use Illuminate\Http\Request;

final class GetAdminIndex
{
    /**
     * @param  list<string>  $allowedSorts
     * @param  list<string>  $allowedFilters
     */
    public static function handle(
        Request $request,
        array $allowedSorts,
        array $allowedFilters,
        string $defaultSort = 'id',
    ): AdminIndexQuery {
        return AdminIndexQuery::fromRequest(
            request: $request,
            allowedSorts: $allowedSorts,
            allowedFilters: $allowedFilters,
            defaultSort: $defaultSort,
        );
    }
}
