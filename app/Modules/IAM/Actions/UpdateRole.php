<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\DTOs\UpdateRoleData;
use App\Modules\IAM\Events\RoleUpdated;
use App\Modules\IAM\Models\Role;
use Illuminate\Support\Facades\DB;

final class UpdateRole
{
    public static function handle(Role $role, UpdateRoleData $data): void
    {
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
}
