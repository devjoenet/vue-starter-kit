<?php

declare(strict_types=1);

namespace Modules\Audit\Listeners;

use Modules\Audit\Actions\RecordAuditLog;
use Modules\Audit\DTOs\AuditLogData;
use Modules\Core\Contracts\AuditableDomainEvent;

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
