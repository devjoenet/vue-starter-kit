<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Contracts;

interface DashboardMetricsProvider
{
    /** @return array{users: int, roles: int, permissions: int} */
    public function counts(): array;
}
