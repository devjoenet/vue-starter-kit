<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Contracts;

interface DashboardMetricCounts
{
    public function users(): int;

    public function roles(): int;

    public function permissions(): int;
}
