<?php

declare(strict_types=1);

namespace App\Modules\Shared\Contracts;

interface AuditableDomainEvent
{
    public function auditEvent(): string;

    public function auditSummary(): string;

    public function auditSubjectType(): ?string;

    public function auditSubjectId(): ?int;

    public function auditSubjectLabel(): ?string;

    /** @return array<string, mixed> */
    public function auditChanges(): array;

    /** @return array<string, mixed> */
    public function auditContext(): array;
}
