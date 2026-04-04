<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\DTOs;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class DashboardMetricSourceData extends Data
{
    public function __construct(
        public int $count,
    ) {}
}
