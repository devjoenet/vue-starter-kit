<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Roles\DTOs\CreateRoleData;
use App\Modules\Roles\Models\Role;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateRole
{
    public static function handle(CreateRoleData $data): Role
    {
        $userIds = self::resolveUserIds($data);

        return DB::transaction(function () use ($data, $userIds): Role {
            $role = self::restoreOrCreateRole($data);
            $role->users()->sync($userIds);

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
}
