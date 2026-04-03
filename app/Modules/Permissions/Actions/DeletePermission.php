<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Permissions\Models\Permission;
use Illuminate\Support\Facades\DB;

final class DeletePermission
{
    public static function handle(Permission $permission): void
    {
        DB::transaction(function () use ($permission): void {
            $before = self::auditState($permission->loadMissing('permissionGroup'));
            $permission->delete();

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: 'permissions.deleted',
                summary: sprintf('Deleted permission %s.', $permission->name),
                subjectType: Permission::class,
                subjectId: (int) $permission->getKey(),
                subjectLabel: $permission->name,
                changes: ['before' => $before, 'after' => null],
            )));
        });
    }

    private static function auditState(Permission $permission): array
    {
        return [
            'name' => $permission->name,
            'label' => $permission->label,
            'description' => $permission->description,
            'group' => $permission->group,
            'group_label' => $permission->group_label,
        ];
    }
}
