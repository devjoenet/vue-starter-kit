<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permissions\Models\Permission;
use Modules\Roles\Models\Role;

final class AdminAclSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionGroupsSeeder::class,
            PermissionsSeeder::class,
            RolesSeeder::class,
        ]);

        $role = Role::withTrashed()->firstOrNew([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);
        $role->forceFill([
            'name' => 'super-admin',
            'guard_name' => 'web',
            'deleted_at' => null,
        ])->save();

        $role->syncPermissions(Permission::all());
    }
}
