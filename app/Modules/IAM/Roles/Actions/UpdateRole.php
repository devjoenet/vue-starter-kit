<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Actions;

use App\Modules\IAM\Roles\DTOs\UpdateRoleData;
use App\Modules\IAM\Roles\Events\RoleUpdated;
use App\Modules\IAM\Roles\Exceptions\CannotRenameProtectedSuperAdminRole;
use App\Modules\IAM\Roles\Models\Role;
use Illuminate\Support\Facades\DB;

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
