<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;
use App\Modules\IAM\Models\Permission;
use App\Modules\IAM\Models\Role;
use App\Modules\Shared\Models\User;

final class DashboardMetrics implements DashboardMetricsProvider
{
    /** @return array{users: int, roles: int, permissions: int} */
    public function counts(): array
    {
        return [
            'users' => User::query()->count(),
            'roles' => Role::query()->count(),
            'permissions' => Permission::query()->count(),
        ];
    }
}
