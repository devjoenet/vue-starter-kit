<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Roles\Models\Role;
use App\Modules\Users\DTOs\SyncUserRolesData;
use App\Modules\Users\Exceptions\UnknownRolesSelected;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;

final class SyncUserRoles
{
    public static function handle(User $user, SyncUserRolesData $data): User
    {
        $roleNames = self::resolveRoleNames($data);
        $before = $user->getRoleNames()->sort()->values()->all();

        DB::transaction(function () use ($user, $roleNames, $before): void {
            $user->syncRoles($roleNames);

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: 'users.roles_synced',
                summary: sprintf('Updated roles for user %s.', $user->email),
                subjectType: User::class,
                subjectId: (int) $user->getKey(),
                subjectLabel: $user->email,
                changes: ['before' => ['roles' => $before], 'after' => ['roles' => $roleNames]],
            )));
        });

        return $user;
    }

    /** @return list<string> */
    private static function resolveRoleNames(SyncUserRolesData $data): array
    {
        $requestedRoles = collect($data->roles)
            ->unique()
            ->values();

        $existingRoles = Role::query()
            ->whereIn('name', $requestedRoles->all())
            ->pluck('name')
            ->values();

        $missingRoles = $requestedRoles
            ->diff($existingRoles)
            ->values()
            ->all();

        if ($missingRoles !== []) {
            throw UnknownRolesSelected::fromNames($missingRoles);
        }

        /** @var list<string> $roleNames */
        $roleNames = $existingRoles->all();

        return $roleNames;
    }
}
