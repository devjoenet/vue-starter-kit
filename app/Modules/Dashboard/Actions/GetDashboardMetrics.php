<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Actions;

use App\Modules\Dashboard\Contracts\DashboardMetricCounts;
use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;

final class GetDashboardMetrics
{
    public static function handle(DashboardMetricsProvider $dashboardMetricsProvider): DashboardMetricCounts
    {
        return $dashboardMetricsProvider->counts();
    }
}
