<?php

declare(strict_types=1);

namespace Modules\Dashboard\Contracts;

interface DashboardMetricsProvider
{
    public function counts(): DashboardMetricCounts;
}
