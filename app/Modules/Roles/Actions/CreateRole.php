<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Roles\DTOs\CreateRoleData;
use App\Modules\Roles\Models\Role;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateRole
{
    public static function handle(CreateRoleData $data): Role
    {
        $userIds = self::resolveUserIds($data);
        $existingRole = Role::withTrashed()
            ->where('name', $data->name)
            ->where('guard_name', 'web')
            ->first();

        return DB::transaction(function () use ($data, $userIds, $existingRole): Role {
            $role = self::restoreOrCreateRole($data);
            $role->users()->sync($userIds);

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: $existingRole?->trashed() ? 'roles.restored' : 'roles.created',
                summary: ($existingRole?->trashed() ? 'Restored' : 'Created').sprintf(' role %s.', $role->name),
                subjectType: Role::class,
                subjectId: (int) $role->getKey(),
                subjectLabel: $role->name,
                changes: [
                    'before' => $existingRole instanceof Role ? self::auditState($existingRole) : null,
                    'after' => self::auditState($role, $userIds),
                ],
            )));

            return $role;
        });
    }

    /** @return list<int> */
    private static function resolveUserIds(CreateRoleData $data): array
    {
        $userIds = collect($data->user_ids)
            ->map(fn (int $userId): int => $userId)
            ->unique()
            ->values()
            ->all();

        if ($userIds === []) {
            return [];
        }

        /** @var list<int> $existingUserIds */
        $existingUserIds = User::query()
            ->whereIn('id', $userIds)
            ->pluck('id')
            ->all();

        return $existingUserIds;
    }

    private static function restoreOrCreateRole(CreateRoleData $data): Role
    {
        $role = Role::withTrashed()->firstOrNew([
            'name' => $data->name,
            'guard_name' => 'web',
        ]);

        $role->forceFill([
            'name' => $data->name,
            'guard_name' => 'web',
            'deleted_at' => null,
        ])->save();

        return $role;
    }

    /** @param  list<int>  $userIds */
    private static function auditState(Role $role, array $userIds = []): array
    {
        return [
            'name' => $role->name,
            'user_ids' => $userIds === []
                ? $role->users()->pluck('users.id')->sort()->values()->all()
                : $userIds,
        ];
    }
}
