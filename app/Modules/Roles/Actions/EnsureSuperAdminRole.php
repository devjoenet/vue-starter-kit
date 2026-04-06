<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Permissions\Models\Permission;
use App\Modules\Roles\Models\Role;

final class EnsureSuperAdminRole
{
    private const string GUARD_NAME = 'web';

    private const string ROLE_NAME = 'super-admin';

    public static function handle(): Role
    {
        $role = Role::withTrashed()->firstOrNew([
            'name' => self::ROLE_NAME,
            'guard_name' => self::GUARD_NAME,
        ]);

        $role->forceFill([
            'name' => self::ROLE_NAME,
            'guard_name' => self::GUARD_NAME,
            'deleted_at' => null,
        ])->save();

        $role->syncPermissions(self::permissionNames());

        return $role;
    }

    public static function name(): string
    {
        return self::ROLE_NAME;
    }

    public static function guardName(): string
    {
        return self::GUARD_NAME;
    }

    /** @return list<string> */
    private static function permissionNames(): array
    {
        return Permission::query()
            ->orderBy('name')
            ->pluck('name')
            ->all();
    }
}
