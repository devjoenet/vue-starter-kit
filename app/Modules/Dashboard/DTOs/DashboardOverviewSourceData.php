<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\DTOs;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class DashboardOverviewSourceData extends Data
{
    public function __construct(
        public int $users,
        public int $roles,
        public int $permissions,
    ) {}
}
