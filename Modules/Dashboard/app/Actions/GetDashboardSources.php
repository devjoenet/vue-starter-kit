<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Modules\Dashboard\Contracts\DashboardMetricsProvider;
use Modules\Dashboard\DTOs\DashboardMetricSourceData;
use Modules\Dashboard\DTOs\DashboardOverviewSourceData;
use Modules\Dashboard\DTOs\DashboardSourcesData;

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
