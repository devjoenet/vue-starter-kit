<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\Events\PermissionDeleted;
use App\Modules\IAM\Models\Permission;
use Illuminate\Support\Facades\DB;

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
