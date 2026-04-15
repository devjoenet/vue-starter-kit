<?php

declare(strict_types=1);

namespace Modules\Audit\DTOs;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class AuditLogIndexFilterOptionsData extends Data
{
    /**
     * @param  list<string>  $actors
     * @param  list<string>  $events
     * @param  list<string>  $subject_types
     */
    public function __construct(
        public array $actors,
        public array $events,
        public array $subject_types,
    ) {}
}
