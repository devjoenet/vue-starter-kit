<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Roles\DTOs\UpdateRoleData;
use App\Modules\Roles\Models\Role;
use Illuminate\Support\Facades\DB;

final class UpdateRole
{
    public static function handle(Role $role, UpdateRoleData $data): Role
    {
        return DB::transaction(function () use ($role, $data): Role {
            $before = self::auditState($role);
            $role->forceFill(['name' => $data->name])->save();

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: 'roles.updated',
                summary: sprintf('Updated role %s.', $role->name),
                subjectType: Role::class,
                subjectId: (int) $role->getKey(),
                subjectLabel: $role->name,
                changes: ['before' => $before, 'after' => self::auditState($role)],
            )));

            return $role;
        });
    }

    private static function auditState(Role $role): array
    {
        return ['name' => $role->name];
    }
}
