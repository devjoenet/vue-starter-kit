<?php

declare(strict_types=1);

namespace App\Modules\Audit\Actions;

use App\Modules\Audit\DTOs\AuditLogIndexFilterOptionsData;
use App\Modules\Audit\Models\AuditLog;

final class GetAuditLogFilterOptions
{
    public static function handle(): AuditLogIndexFilterOptionsData
    {
        return new AuditLogIndexFilterOptionsData(
            actors: self::distinctValues('actor_label'),
            events: self::distinctValues('event'),
            subject_types: self::distinctValues('subject_type'),
        );
    }

    /** @return list<string> */
    private static function distinctValues(string $column): array
    {
        return AuditLog::query()
            ->whereNotNull($column)
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->all();
    }
}
