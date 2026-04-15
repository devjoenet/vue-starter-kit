<?php

declare(strict_types=1);

namespace Modules\Audit\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Modules\Audit\DTOs\AuditLogData;
use Modules\Audit\Models\AuditLog;
use Modules\Core\Models\User;

final class RecordAuditLog
{
    public static function handle(AuditLogData $data, ?Request $request = null): void
    {
        $request ??= request();
        $actor = $request->user();

        AuditLog::query()->create([
            'actor_type' => is_object($actor) ? $actor::class : null,
            'actor_id' => is_object($actor) ? $actor->getAuthIdentifier() : null,
            'actor_label' => self::resolveActorLabel($actor),
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

    private static function resolveActorLabel(?Authenticatable $actor): ?string
    {
        if (! $actor instanceof Authenticatable) {
            return null;
        }

        if ($actor instanceof User) {
            return $actor->email;
        }

        return (string) $actor->getAuthIdentifier();
    }
}
