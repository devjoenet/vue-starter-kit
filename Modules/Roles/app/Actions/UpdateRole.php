<?php

declare(strict_types=1);

namespace Modules\Roles\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Roles\DTOs\UpdateRoleData;
use Modules\Roles\Events\RoleUpdated;
use Modules\Roles\Exceptions\CannotRenameProtectedSuperAdminRole;
use Modules\Roles\Models\Role;

final class UpdateRole
{
    public static function handle(Role $role, UpdateRoleData $data): void
    {
        self::ensureRoleNameCanBeUpdated($role, $data->name);

        DB::transaction(function () use ($role, $data): void {
            $before = self::auditState($role);
            $role->forceFill(['name' => $data->name])->save();

            event(new RoleUpdated($role, $before));
        });
    }

    private static function auditState(Role $role): array
    {
        return ['name' => $role->name];
    }

    private static function ensureRoleNameCanBeUpdated(Role $role, string $requestedName): void
    {
        if (! EnsureSuperAdminRole::is($role) && $requestedName !== EnsureSuperAdminRole::name()) {
            return;
        }

        if (EnsureSuperAdminRole::is($role) && $requestedName === $role->name) {
            return;
        }

        throw CannotRenameProtectedSuperAdminRole::forRole($role, $requestedName);
    }
}
