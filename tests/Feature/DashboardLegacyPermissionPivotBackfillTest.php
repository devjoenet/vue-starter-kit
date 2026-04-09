<?php

declare(strict_types=1);

use App\Modules\IAM\Models\Permission;
use App\Modules\IAM\Models\PermissionGroup;
use App\Modules\IAM\Models\Role;
use App\Modules\Shared\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\PermissionRegistrar;

test('legacy permission pivot model types are backfilled for dashboard auth data', function (): void {
    // Arrange

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $adminGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'admin'],
        ['label' => 'Admin'],
    );

    $usersViewPermission = Permission::query()->create([
        'name' => 'users.view',
        'permission_group_id' => $adminGroup->id,
        'guard_name' => 'web',
    ]);

    $rolesViewPermission = Permission::query()->create([
        'name' => 'roles.view',
        'permission_group_id' => $adminGroup->id,
        'guard_name' => 'web',
    ]);

    $permissionsViewPermission = Permission::query()->create([
        'name' => 'permissions.view',
        'permission_group_id' => $adminGroup->id,
        'guard_name' => 'web',
    ]);

    $usersCreatePermission = Permission::query()->create([
        'name' => 'users.create',
        'permission_group_id' => $adminGroup->id,
        'guard_name' => 'web',
    ]);

    $role = Role::query()->create([
        'name' => 'workspace-admin',
        'guard_name' => 'web',
    ]);

    $role->givePermissionTo([
        $usersViewPermission,
        $rolesViewPermission,
        $permissionsViewPermission,
    ]);

    $user = User::factory()->create();

    DB::table('model_has_roles')->insert([
        'role_id' => $role->id,
        'model_id' => $user->id,
        'model_type' => 'App\\Models\\User',
    ]);

    DB::table('model_has_permissions')->insert([
        'permission_id' => $usersCreatePermission->id,
        'model_id' => $user->id,
        'model_type' => 'App\\Models\\User',
    ]);

    $migration = require database_path('migrations/2026_03_30_220000_backfill_legacy_permission_pivot_model_types.php');

    // Act

    $migration->up();

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    // Assert

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Dashboard')
            ->where('auth.roles', ['workspace-admin'])
            ->where(
                'auth.permissions',
                fn ($permissions): bool => collect($permissions)->sort()->values()->all() === [
                    'permissions.view',
                    'roles.view',
                    'users.create',
                    'users.view',
                ],
            )
        );

    expect(DB::table('model_has_roles')->where('model_type', 'App\\Models\\User')->exists())->toBeFalse();
    expect(DB::table('model_has_permissions')->where('model_type', 'App\\Models\\User')->exists())->toBeFalse();
    expect(DB::table('model_has_roles')->where('model_type', User::class)->exists())->toBeTrue();
    expect(DB::table('model_has_permissions')->where('model_type', User::class)->exists())->toBeTrue();
});
