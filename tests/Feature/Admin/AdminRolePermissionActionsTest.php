<?php

declare(strict_types=1);

use App\Actions\Admin\Permissions\CreatePermission;
use App\Actions\Admin\Permissions\DeletePermission;
use App\Actions\Admin\Permissions\UpdatePermission;
use App\Actions\Admin\Roles\CreateRole;
use App\Actions\Admin\Roles\DeleteRole;
use App\Actions\Admin\Roles\SyncRolePermissions;
use App\Actions\Admin\Roles\UpdateRole;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\Data\Admin\Permissions\CreatePermissionData;
use App\Support\Data\Admin\Permissions\UpdatePermissionData;
use App\Support\Data\Admin\Roles\CreateRoleData;
use App\Support\Data\Admin\Roles\SyncRolePermissionsData;
use App\Support\Data\Admin\Roles\UpdateRoleData;
use App\Support\Exceptions\UnknownPermissionsSelected;

test('create role action persists a role and syncs users', function (): void {
    $users = User::factory()->count(2)->create();

    $role = app(CreateRole::class)->handle(new CreateRoleData(
        name: 'auditor',
        user_ids: $users->pluck('id')->all(),
    ));

    expect($role->exists)->toBeTrue();
    expect($users[0]->fresh()?->hasRole('auditor'))->toBeTrue();
    expect($users[1]->fresh()?->hasRole('auditor'))->toBeTrue();
});

test('update role action updates the role name', function (): void {
    $role = Role::query()->create([
        'name' => 'old-role',
        'guard_name' => 'web',
    ]);

    app(UpdateRole::class)->handle($role, new UpdateRoleData(
        name: 'new-role',
    ));

    expect($role->fresh()?->name)->toBe('new-role');
});

test('delete role action removes the role', function (): void {
    $role = Role::query()->create([
        'name' => 'temporary-role',
        'guard_name' => 'web',
    ]);

    app(DeleteRole::class)->handle($role);

    expect(Role::query()->find($role->id))->toBeNull();
});

test('sync role permissions action syncs the selected permissions by name', function (): void {
    $role = Role::query()->create([
        'name' => 'editor',
        'guard_name' => 'web',
    ]);

    Permission::query()->create([
        'name' => 'users.viewReports',
        'group' => 'users',
        'guard_name' => 'web',
    ]);

    Permission::query()->create([
        'name' => 'users.manageMembers',
        'group' => 'users',
        'guard_name' => 'web',
    ]);

    app(SyncRolePermissions::class)->handle($role, new SyncRolePermissionsData(
        permissions: [
            'users.viewReports',
            'users.manageMembers',
        ],
    ));

    expect($role->fresh()?->permissions->pluck('name')->sort()->values()->all())->toBe([
        'users.manageMembers',
        'users.viewReports',
    ]);
});

test('sync role permissions action throws a domain exception when permissions are missing', function (): void {
    $role = Role::query()->create([
        'name' => 'editor',
        'guard_name' => 'web',
    ]);

    try {
        app(SyncRolePermissions::class)->handle($role, new SyncRolePermissionsData(
            permissions: ['users.missingPermission'],
        ));

        $this->fail('Expected unknown permissions exception was not thrown.');
    } catch (UnknownPermissionsSelected $exception) {
        expect($exception->permissionNames)->toBe(['users.missingPermission']);
        expect($exception->errors())->toBe([
            'permissions' => ['One or more selected permissions are invalid.'],
        ]);
    }
});

test('create permission action persists a permission', function (): void {
    $permission = app(CreatePermission::class)->handle(new CreatePermissionData(
        name: 'users.manageMembers',
        group: 'users',
    ));

    expect($permission->exists)->toBeTrue();
    expect($permission->guard_name)->toBe('web');
});

test('update permission action updates name and group', function (): void {
    $permission = Permission::query()->create([
        'name' => 'users.viewReports',
        'group' => 'users',
        'guard_name' => 'web',
    ]);

    app(UpdatePermission::class)->handle($permission, new UpdatePermissionData(
        name: 'roles.manageUsers',
        group: 'roles',
    ));

    expect($permission->fresh()?->name)->toBe('roles.manageUsers');
    expect($permission->fresh()?->group)->toBe('roles');
});

test('delete permission action removes the permission', function (): void {
    $permission = Permission::query()->create([
        'name' => 'users.removeMembers',
        'group' => 'users',
        'guard_name' => 'web',
    ]);

    app(DeletePermission::class)->handle($permission);

    expect(Permission::query()->find($permission->id))->toBeNull();
});
