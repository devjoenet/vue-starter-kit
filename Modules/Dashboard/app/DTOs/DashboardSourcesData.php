<?php

declare(strict_types=1);

namespace Modules\Dashboard\DTOs;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class DashboardSourcesData extends Data
{
    public function __construct(
        public DashboardOverviewSourceData $overview,
        public DashboardMetricSourceData $users,
        public DashboardMetricSourceData $roles,
        public DashboardMetricSourceData $permissions,
    ) {}
}
