<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\IAM\Models\Permission;
use App\Modules\IAM\Models\Role;
use Illuminate\Database\Seeder;

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
