<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\Actions;

use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Roles\Models\Role;
use App\Modules\Users\Models\User;

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
