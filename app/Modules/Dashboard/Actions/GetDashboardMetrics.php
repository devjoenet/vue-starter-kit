<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Actions;

use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;

final class GetDashboardMetrics
{
    /** @return array{users: int, roles: int, permissions: int} */
    public static function handle(DashboardMetricsProvider $dashboardMetricsProvider): array
    {
        return $dashboardMetricsProvider->counts();
    }
}
