<?php

declare(strict_types=1);

namespace App\Modules\Shared\Events;

use App\Modules\Shared\Contracts\AuditableDomainEvent;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

abstract readonly class AbstractAuditableEvent implements AuditableDomainEvent, ShouldDispatchAfterCommit
{
    /**
     * @param  array<string, mixed>  $changes
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        private string $event,
        private string $summary,
        private ?string $subjectType = null,
        private ?int $subjectId = null,
        private ?string $subjectLabel = null,
        private array $changes = [],
        private array $context = [],
    ) {}

    public function auditEvent(): string
    {
        return $this->event;
    }

    public function auditSummary(): string
    {
        return $this->summary;
    }

    public function auditSubjectType(): ?string
    {
        return $this->subjectType;
    }

    public function auditSubjectId(): ?int
    {
        return $this->subjectId;
    }

    public function auditSubjectLabel(): ?string
    {
        return $this->subjectLabel;
    }

    /** @return array<string, mixed> */
    public function auditChanges(): array
    {
        return $this->changes;
    }

    /** @return array<string, mixed> */
    public function auditContext(): array
    {
        return $this->context;
    }
}
