<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Modules\Dashboard\Contracts\DashboardMetricCounts;
use Modules\Dashboard\Contracts\DashboardMetricsProvider;

final class GetDashboardMetrics
{
    public static function handle(DashboardMetricsProvider $dashboardMetricsProvider): DashboardMetricCounts
    {
        return $dashboardMetricsProvider->counts();
    }
}
