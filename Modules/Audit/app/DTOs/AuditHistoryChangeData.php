<?php

declare(strict_types=1);

namespace Modules\Audit\DTOs;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class AuditHistoryChangeData extends Data
{
    public function __construct(
        public string $field,
        public ?string $before,
        public ?string $after,
    ) {}
}
