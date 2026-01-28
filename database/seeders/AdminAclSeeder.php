<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AdminPermission;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

final class AdminAclSeeder extends Seeder
{
    public function run(): void
    {
        foreach (AdminPermission::cases() as $perm) {
            Permission::query()->firstOrCreate(
                ['name' => $perm->value, 'guard_name' => 'web'],
                ['group' => $perm->group()],
            );
        }

        $role = Role::query()->firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $role->syncPermissions(Permission::all());
    }
}
