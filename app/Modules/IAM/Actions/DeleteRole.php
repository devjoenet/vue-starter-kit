<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\Events\RoleDeleted;
use App\Modules\IAM\Exceptions\CannotDeleteProtectedSuperAdminRole;
use App\Modules\IAM\Models\Role;
use Illuminate\Support\Facades\DB;

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
