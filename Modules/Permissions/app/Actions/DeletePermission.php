<?php

declare(strict_types=1);

namespace Modules\Permissions\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Permissions\Events\PermissionDeleted;
use Modules\Permissions\Models\Permission;

final class DeletePermission
{
    public static function handle(Permission $permission): void
    {
        DB::transaction(function () use ($permission): void {
            $before = self::auditState($permission->loadMissing('permissionGroup'));
            $permission->delete();

            event(new PermissionDeleted($permission, $before));
        });
    }

    private static function auditState(Permission $permission): array
    {
        return [
            'name' => $permission->name,
            'label' => $permission->label,
            'description' => $permission->description,
            'group' => $permission->group,
            'group_label' => $permission->group_label,
        ];
    }
}
