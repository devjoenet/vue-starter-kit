<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\User;
use Modules\Roles\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminAclSeeder::class);

        if (app()->isProduction()) {
            return;
        }

        $user = User::withTrashed()->firstOrNew([
            'email' => 'test@example.com',
        ]);

        $user->forceFill([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'deleted_at' => null,
        ])->save();

        $superAdminRole = Role::query()
            ->where('name', 'super-admin')
            ->where('guard_name', 'web')
            ->first();

        if ($superAdminRole instanceof Role) {
            $user->assignRole($superAdminRole);
        }
    }
}
