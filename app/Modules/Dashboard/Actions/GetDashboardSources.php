<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Actions;

use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;
use App\Modules\Dashboard\DTOs\DashboardMetricSourceData;
use App\Modules\Dashboard\DTOs\DashboardOverviewSourceData;
use App\Modules\Dashboard\DTOs\DashboardSourcesData;

final class GetDashboardSources
{
    public static function handle(DashboardMetricsProvider $dashboardMetricsProvider): DashboardSourcesData
    {
        $counts = GetDashboardMetrics::handle($dashboardMetricsProvider);

        return new DashboardSourcesData(
            overview: new DashboardOverviewSourceData(
                users: $counts->users(),
                roles: $counts->roles(),
                permissions: $counts->permissions(),
            ),
            users: new DashboardMetricSourceData(count: $counts->users()),
            roles: new DashboardMetricSourceData(count: $counts->roles()),
            permissions: new DashboardMetricSourceData(count: $counts->permissions()),
        );
    }
}
