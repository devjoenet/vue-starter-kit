<?php

declare(strict_types=1);

namespace App\Modules\Audit\DTOs;

final readonly class AuditLogData
{
    /**
     * @param  array<string, mixed>  $changes
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public string $event,
        public string $summary,
        public ?string $subjectType = null,
        public ?int $subjectId = null,
        public ?string $subjectLabel = null,
        public array $changes = [],
        public array $context = [],
    ) {}
}
