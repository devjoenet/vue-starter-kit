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
use App\Models\PermissionGroup;
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

test('create role action restores a soft deleted role with the same name', function (): void {
    $role = Role::query()->create([
        'name' => 'auditor',
        'guard_name' => 'web',
    ]);

    $role->delete();

    $restoredRole = app(CreateRole::class)->handle(new CreateRoleData(
        name: 'auditor',
        user_ids: [],
    ));

    expect($restoredRole->id)->toBe($role->id);
    expect($restoredRole->trashed())->toBeFalse();
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

test('delete role action soft deletes the role', function (): void {
    $role = Role::query()->create([
        'name' => 'temporary-role',
        'guard_name' => 'web',
    ]);

    app(DeleteRole::class)->handle($role);

    expect(Role::query()->find($role->id))->toBeNull();
    expect(Role::withTrashed()->find($role->id)?->trashed())->toBeTrue();
});

test('sync role permissions action syncs the selected permissions by name', function (): void {
    $role = Role::query()->create([
        'name' => 'editor',
        'guard_name' => 'web',
    ]);
    $usersGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'users'],
        ['label' => 'Users'],
    );

    Permission::query()->create([
        'name' => 'users.viewReports',
        'permission_group_id' => $usersGroup->id,
        'guard_name' => 'web',
    ]);

    Permission::query()->create([
        'name' => 'users.manageMembers',
        'permission_group_id' => $usersGroup->id,
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
        label: 'Manage Members',
        description: 'Create, update, and remove member relationships.',
        group: 'users',
        groupLabel: 'User Administration',
        groupDescription: 'Identity, lifecycle, and role assignment controls.',
    ));

    expect($permission->exists)->toBeTrue();
    expect($permission->guard_name)->toBe('web');
    expect($permission->label)->toBe('Manage Members');
    expect($permission->permissionGroup?->label)->toBe('User Administration');
});

test('create permission action restores a soft deleted permission with the same key', function (): void {
    $usersGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'users'],
        ['label' => 'Users'],
    );

    $permission = Permission::query()->create([
        'name' => 'users.manageMembers',
        'label' => 'Manage Members',
        'permission_group_id' => $usersGroup->id,
        'guard_name' => 'web',
    ]);

    $permission->delete();

    $restoredPermission = app(CreatePermission::class)->handle(new CreatePermissionData(
        name: 'users.manageMembers',
        label: 'Manage Team Members',
        description: 'Restore the permission with updated catalog copy.',
        group: 'users',
        groupLabel: 'User Administration',
        groupDescription: 'Identity, lifecycle, and role assignment controls.',
    ));

    expect($restoredPermission->id)->toBe($permission->id);
    expect($restoredPermission->trashed())->toBeFalse();
    expect($restoredPermission->label)->toBe('Manage Team Members');
});

test('update permission action keeps the key stable while updating catalog metadata', function (): void {
    $permissionGroup = PermissionGroup::query()->create([
        'slug' => 'users',
        'label' => 'User Administration',
        'description' => 'Identity and account lifecycle controls.',
    ]);

    $permission = Permission::query()->create([
        'name' => 'users.viewReports',
        'label' => 'View Reports',
        'permission_group_id' => $permissionGroup->id,
        'guard_name' => 'web',
    ]);

    app(UpdatePermission::class)->handle($permission, new UpdatePermissionData(
        label: 'Manage Report Access',
        description: 'Review and maintain report visibility across teams.',
        group: 'roles',
        groupLabel: 'Role Management',
        groupDescription: 'Role creation, maintenance, and permission-footprint controls.',
    ));

    expect($permission->fresh()?->name)->toBe('users.viewReports');
    expect($permission->fresh()?->label)->toBe('Manage Report Access');
    expect($permission->fresh()?->description)->toBe('Review and maintain report visibility across teams.');
    expect($permission->fresh()?->group)->toBe('roles');
    expect($permission->fresh()?->permissionGroup?->label)->toBe('Role Management');
});

test('delete permission action soft deletes the permission', function (): void {
    $usersGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'users'],
        ['label' => 'Users'],
    );

    $permission = Permission::query()->create([
        'name' => 'users.removeMembers',
        'permission_group_id' => $usersGroup->id,
        'guard_name' => 'web',
    ]);

    app(DeletePermission::class)->handle($permission);

    expect(Permission::query()->find($permission->id))->toBeNull();
    expect(Permission::withTrashed()->find($permission->id)?->trashed())->toBeTrue();
});
