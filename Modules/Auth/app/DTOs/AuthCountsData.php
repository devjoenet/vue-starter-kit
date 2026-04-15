<?php

declare(strict_types=1);

namespace Modules\Auth\DTOs;

use Modules\Dashboard\Contracts\DashboardMetricCounts;
use Spatie\LaravelData\Data;

final class AuthCountsData extends Data implements DashboardMetricCounts
{
    public function __construct(
        public int $users,
        public int $roles,
        public int $permissions,
    ) {}

    public function users(): int
    {
        return $this->users;
    }

    public function roles(): int
    {
        return $this->roles;
    }

    public function permissions(): int
    {
        return $this->permissions;
    }
}
