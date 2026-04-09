<?php

declare(strict_types=1);

namespace App\Modules\Audit\Listeners;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Shared\Contracts\AuditableDomainEvent;

final class RecordAuditableDomainEvent
{
    public function handle(AuditableDomainEvent $event): void
    {
        RecordAuditLog::handle(new AuditLogData(
            event: $event->auditEvent(),
            summary: $event->auditSummary(),
            subjectType: $event->auditSubjectType(),
            subjectId: $event->auditSubjectId(),
            subjectLabel: $event->auditSubjectLabel(),
            changes: $event->auditChanges(),
            context: $event->auditContext(),
        ));
    }
}
