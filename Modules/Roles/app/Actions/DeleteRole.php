<?php

declare(strict_types=1);

namespace Modules\Roles\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Roles\Events\RoleDeleted;
use Modules\Roles\Exceptions\CannotDeleteProtectedSuperAdminRole;
use Modules\Roles\Models\Role;

final class DeleteRole
{
    public static function handle(Role $role): void
    {
        self::ensureRoleCanBeDeleted($role);

        DB::transaction(function () use ($role): void {
            $before = self::auditState($role);
            $role->delete();

            event(new RoleDeleted($role, $before));
        });
    }

    private static function auditState(Role $role): array
    {
        return ['name' => $role->name];
    }

    private static function ensureRoleCanBeDeleted(Role $role): void
    {
        if (! EnsureSuperAdminRole::is($role)) {
            return;
        }

        throw CannotDeleteProtectedSuperAdminRole::forRole($role);
    }
}
