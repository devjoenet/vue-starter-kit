<?php

declare(strict_types=1);

namespace Modules\Auth\Actions;

use Modules\Auth\DTOs\AuthCountsData;
use Modules\Dashboard\Contracts\DashboardMetricCounts;
use Modules\Dashboard\Contracts\DashboardMetricsProvider;
use Modules\Permissions\Actions\CountPermissions;
use Modules\Roles\Actions\CountRoles;
use Modules\Users\Actions\CountUsers;

final class GetAuthCounts implements DashboardMetricsProvider
{
    public function counts(): DashboardMetricCounts
    {
        return self::handle();
    }

    public static function handle(): AuthCountsData
    {
        return new AuthCountsData(
            users: CountUsers::handle(),
            roles: CountRoles::handle(),
            permissions: CountPermissions::handle(),
        );
    }
}
