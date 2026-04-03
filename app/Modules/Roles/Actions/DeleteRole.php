<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Roles\Models\Role;
use Illuminate\Support\Facades\DB;

final class DeleteRole
{
    public static function handle(Role $role): void
    {
        DB::transaction(function () use ($role): void {
            $before = self::auditState($role);
            $role->delete();

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: 'roles.deleted',
                summary: sprintf('Deleted role %s.', $role->name),
                subjectType: Role::class,
                subjectId: (int) $role->getKey(),
                subjectLabel: $role->name,
                changes: ['before' => $before, 'after' => null],
            )));
        });
    }

    private static function auditState(Role $role): array
    {
        return ['name' => $role->name];
    }
}
