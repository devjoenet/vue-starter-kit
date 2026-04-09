<?php

declare(strict_types=1);

namespace App\Modules\Audit\Actions;

use App\Modules\Audit\DTOs\AuditLogIndexQueryData;
use App\Modules\Audit\Requests\IndexAuditLogsRequest;

final class IndexAuditLogs
{
    public static function handle(IndexAuditLogsRequest $request): array
    {
        $query = AuditLogIndexQueryData::fromRequest($request);

        return [
            'auditLogs' => GetAuditLogIndexItems::handle($query),
            'filterOptions' => GetAuditLogFilterOptions::handle(),
            'query' => $query,
        ];
    }
}
