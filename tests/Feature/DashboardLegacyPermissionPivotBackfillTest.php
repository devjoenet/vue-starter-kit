<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Core\Models\User;
use Modules\Permissions\Models\Permission;
use Modules\Permissions\Models\PermissionGroup;
use Modules\Roles\Models\Role;
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
            ->component('Dashboard/Index')
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

test('intermediate module permission pivot model types are backfilled for dashboard auth data', function (): void {
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

    $usersCreatePermission = Permission::query()->create([
        'name' => 'users.create',
        'permission_group_id' => $adminGroup->id,
        'guard_name' => 'web',
    ]);

    $role = Role::query()->create([
        'name' => 'workspace-admin',
        'guard_name' => 'web',
    ]);

    $role->givePermissionTo($usersViewPermission);

    $user = User::factory()->create();

    DB::table('model_has_roles')->insert([
        'role_id' => $role->id,
        'model_id' => $user->id,
        'model_type' => 'App\\Modules\\Users\\Models\\User',
    ]);

    DB::table('model_has_permissions')->insert([
        'permission_id' => $usersCreatePermission->id,
        'model_id' => $user->id,
        'model_type' => 'App\\Modules\\Users\\Models\\User',
    ]);

    $migration = require database_path('migrations/2026_04_09_020000_backfill_module_user_permission_pivot_model_types.php');

    // Act

    $migration->up();

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    // Assert

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Index')
            ->where('auth.roles', ['workspace-admin'])
            ->where(
                'auth.permissions',
                fn ($permissions): bool => collect($permissions)->sort()->values()->all() === [
                    'users.create',
                    'users.view',
                ],
            )
        );

    expect(DB::table('model_has_roles')->where('model_type', 'App\\Modules\\Users\\Models\\User')->exists())->toBeFalse();
    expect(DB::table('model_has_permissions')->where('model_type', 'App\\Modules\\Users\\Models\\User')->exists())->toBeFalse();
    expect(DB::table('model_has_roles')->where('model_type', User::class)->exists())->toBeTrue();
    expect(DB::table('model_has_permissions')->where('model_type', User::class)->exists())->toBeTrue();
});

test('shared module permission pivot model types are backfilled for dashboard auth data', function (): void {
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

    $auditLogsViewPermission = Permission::query()->create([
        'name' => 'audit_logs.view',
        'permission_group_id' => $adminGroup->id,
        'guard_name' => 'web',
    ]);

    $role = Role::query()->create([
        'name' => 'workspace-admin',
        'guard_name' => 'web',
    ]);

    $role->givePermissionTo($usersViewPermission);

    $user = User::factory()->create();

    DB::table('model_has_roles')->insert([
        'role_id' => $role->id,
        'model_id' => $user->id,
        'model_type' => 'App\\Modules\\Shared\\Models\\User',
    ]);

    DB::table('model_has_permissions')->insert([
        'permission_id' => $auditLogsViewPermission->id,
        'model_id' => $user->id,
        'model_type' => 'App\\Modules\\Shared\\Models\\User',
    ]);

    $migration = require database_path('migrations/2026_04_13_090000_backfill_shared_module_user_permission_pivot_model_types.php');

    // Act

    $migration->up();

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    // Assert

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Index')
            ->where('auth.roles', ['workspace-admin'])
            ->where(
                'auth.permissions',
                fn ($permissions): bool => collect($permissions)->sort()->values()->all() === [
                    'audit_logs.view',
                    'users.view',
                ],
            )
        );

    expect(DB::table('model_has_roles')->where('model_type', 'App\\Modules\\Shared\\Models\\User')->exists())->toBeFalse();
    expect(DB::table('model_has_permissions')->where('model_type', 'App\\Modules\\Shared\\Models\\User')->exists())->toBeFalse();
    expect(DB::table('model_has_roles')->where('model_type', User::class)->exists())->toBeTrue();
    expect(DB::table('model_has_permissions')->where('model_type', User::class)->exists())->toBeTrue();
});
