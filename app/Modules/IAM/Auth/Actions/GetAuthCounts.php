<?php

declare(strict_types=1);

namespace App\Modules\IAM\Auth\Actions;

use App\Modules\Dashboard\Contracts\DashboardMetricCounts;
use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;
use App\Modules\IAM\Auth\DTOs\AuthCountsData;
use App\Modules\IAM\Permissions\Actions\CountPermissions;
use App\Modules\IAM\Roles\Actions\CountRoles;
use App\Modules\IAM\Users\Actions\CountUsers;

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
