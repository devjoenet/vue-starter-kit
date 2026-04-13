<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Contracts;

interface DashboardMetricsProvider
{
    public function counts(): DashboardMetricCounts;
}
