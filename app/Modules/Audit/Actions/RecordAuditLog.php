<?php

declare(strict_types=1);

namespace App\Modules\Audit\Actions;

use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use Illuminate\Http\Request;

final class RecordAuditLog
{
    public static function handle(AuditLogData $data, ?Request $request = null): AuditLog
    {
        $request ??= request();
        $actor = $request->user();

        return AuditLog::query()->create([
            'actor_type' => is_object($actor) ? $actor::class : null,
            'actor_id' => is_object($actor) ? $actor->getAuthIdentifier() : null,
            'event' => $data->event,
            'subject_type' => $data->subjectType,
            'subject_id' => $data->subjectId,
            'subject_label' => $data->subjectLabel,
            'summary' => $data->summary,
            'changes' => $data->changes === [] ? null : $data->changes,
            'context' => $data->context === [] ? null : $data->context,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);
    }
}
