<?php

declare(strict_types=1);

namespace App\Modules\Audit\Actions;

use App\Modules\Audit\DTOs\AuditHistoryItemData;
use App\Modules\Audit\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class GetAuditHistoryItems
{
    /** @return Collection<int, AuditHistoryItemData> */
    public static function handle(Model $subject, int $limit = 12): Collection
    {
        /** @var Collection<int, AuditHistoryItemData> $auditLogs */
        $auditLogs = AuditHistoryItemData::collect(
            AuditLog::query()
                ->select([
                    'audit_logs.id',
                    'audit_logs.created_at',
                    'audit_logs.event',
                    'audit_logs.summary',
                    'audit_logs.actor_label',
                    'audit_logs.changes',
                ])
                ->where('audit_logs.subject_type', $subject::class)
                ->where('audit_logs.subject_id', $subject->getKey())
                ->orderByDesc('audit_logs.created_at')
                ->orderByDesc('audit_logs.id')
                ->limit($limit)
                ->get(),
            Collection::class,
        );

        return $auditLogs;
    }
}
