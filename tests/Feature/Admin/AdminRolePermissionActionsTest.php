<?php

declare(strict_types=1);

use App\Modules\IAM\Permissions\Actions\CreatePermission;
use App\Modules\IAM\Permissions\Actions\DeletePermission;
use App\Modules\IAM\Permissions\Actions\UpdatePermission;
use App\Modules\IAM\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\IAM\Permissions\DTOs\CreatePermissionData;
use App\Modules\IAM\Permissions\DTOs\UpdatePermissionData;
use App\Modules\IAM\Permissions\Events\PermissionUpdated;
use App\Modules\IAM\Permissions\Exceptions\CannotRemoveRequiredSuperAdminPermissions;
use App\Modules\IAM\Permissions\Exceptions\UnknownPermissionsSelected;
use App\Modules\IAM\Permissions\Models\Permission;
use App\Modules\IAM\Permissions\Models\PermissionGroup;
use App\Modules\IAM\Roles\Actions\CreateRole;
use App\Modules\IAM\Roles\Actions\DeleteRole;
use App\Modules\IAM\Roles\Actions\EnsureSuperAdminRole;
use App\Modules\IAM\Roles\Actions\SyncRolePermissions;
use App\Modules\IAM\Roles\Actions\UpdateRole;
use App\Modules\IAM\Roles\DTOs\CreateRoleData;
use App\Modules\IAM\Roles\DTOs\SyncRolePermissionsData;
use App\Modules\IAM\Roles\DTOs\UpdateRoleData;
use App\Modules\IAM\Roles\Events\RolePermissionsSynced;
use App\Modules\IAM\Roles\Exceptions\CannotDeleteProtectedSuperAdminRole;
use App\Modules\IAM\Roles\Exceptions\CannotRenameProtectedSuperAdminRole;
use App\Modules\IAM\Roles\Models\Role;
use App\Modules\Shared\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

test('create role action persists a role and syncs users', function (): void {
    $users = User::factory()->count(2)->create();

    $role = CreateRole::handle(new CreateRoleData(
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

    $restoredRole = CreateRole::handle(new CreateRoleData(
        name: 'auditor',
        user_ids: [],
    ));

    expect($restoredRole->id)->toBe($role->id);
    expect($restoredRole->trashed())->toBeFalse();
});

test('create role action syncs only active unique users', function (): void {
    $activeUser = User::factory()->create();
    $archivedUser = User::factory()->create();
    $archivedUser->delete();

    $role = CreateRole::handle(new CreateRoleData(
        name: 'auditor',
        user_ids: [$activeUser->id, $activeUser->id, $archivedUser->id, 999999],
    ));

    expect(DB::table('model_has_roles')
        ->where('role_id', $role->id)
        ->where('model_type', User::class)
        ->pluck('model_id')
        ->all())->toBe([$activeUser->id]);
    expect($activeUser->fresh()?->hasRole('auditor'))->toBeTrue();
    expect(User::withTrashed()->find($archivedUser->id)?->hasRole('auditor'))->toBeFalse();
});

test('update role action updates the role name', function (): void {
    $role = Role::query()->create([
        'name' => 'old-role',
        'guard_name' => 'web',
    ]);

    UpdateRole::handle($role, new UpdateRoleData(
        name: 'new-role',
    ));

    expect($role->fresh()?->name)->toBe('new-role');
});

test('update role action blocks renaming the protected super-admin role', function (): void {
    $role = EnsureSuperAdminRole::handle();

    try {
        UpdateRole::handle($role, new UpdateRoleData(
            name: 'chief-admin',
        ));

        $this->fail('Expected protected super-admin role rename exception was not thrown.');
    } catch (CannotRenameProtectedSuperAdminRole $exception) {
        expect($exception->roleId)->toBe($role->id);
        expect($exception->currentName)->toBe('super-admin');
        expect($exception->requestedName)->toBe('chief-admin');
        expect($exception->errors())->toBe([
            'name' => ['The protected super-admin role name is reserved and cannot be reassigned.'],
        ]);
        expect($exception->context()['role_id'])->toBe($role->id);
        expect($exception->context()['current_name'])->toBe('super-admin');
        expect($exception->context()['requested_name'])->toBe('chief-admin');
        expect($exception->context()['error_fields'])->toBe(['name']);
    }
});

test('delete role action soft deletes the role', function (): void {
    $role = Role::query()->create([
        'name' => 'temporary-role',
        'guard_name' => 'web',
    ]);

    DeleteRole::handle($role);

    expect(Role::query()->find($role->id))->toBeNull();
    expect(Role::withTrashed()->find($role->id)?->trashed())->toBeTrue();
});

test('delete role action blocks deleting the protected super-admin role', function (): void {
    $role = EnsureSuperAdminRole::handle();

    try {
        DeleteRole::handle($role);

        $this->fail('Expected protected super-admin role deletion exception was not thrown.');
    } catch (CannotDeleteProtectedSuperAdminRole $exception) {
        expect($exception->roleId)->toBe($role->id);
        expect($exception->roleName)->toBe('super-admin');
        expect($exception->errors())->toBe([
            'role' => ['The protected super-admin role cannot be deleted.'],
        ]);
        expect($exception->context()['role_id'])->toBe($role->id);
        expect($exception->context()['role_name'])->toBe('super-admin');
        expect($exception->context()['error_fields'])->toBe(['role']);
    }
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

    SyncRolePermissions::handle($role, new SyncRolePermissionsData(
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

test('sync role permissions action dispatches an auditable sync event', function (): void {
    Event::fake([RolePermissionsSynced::class]);

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

    SyncRolePermissions::handle($role, new SyncRolePermissionsData(
        permissions: ['users.viewReports'],
    ));

    Event::assertDispatched(RolePermissionsSynced::class, fn (RolePermissionsSynced $event): bool => $event->auditEvent() === 'roles.permissions_synced'
        && $event->auditSubjectId() === $role->id);
});

test('sync role permissions action throws a domain exception when permissions are missing', function (): void {
    $role = Role::query()->create([
        'name' => 'editor',
        'guard_name' => 'web',
    ]);

    try {
        SyncRolePermissions::handle($role, new SyncRolePermissionsData(
            permissions: ['users.missingPermission'],
        ));

        $this->fail('Expected unknown permissions exception was not thrown.');
    } catch (UnknownPermissionsSelected $exception) {
        expect($exception->permissionNames)->toBe(['users.missingPermission']);
        expect($exception->errors())->toBe([
            'permissions' => ['One or more selected permissions are invalid.'],
        ]);
        expect($exception->context()['permission_names'])->toBe(['users.missingPermission']);
        expect($exception->context()['error_fields'])->toBe(['permissions']);
    }
});

test('sync role permissions action blocks narrowing the protected super-admin role', function (): void {
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

    $role = EnsureSuperAdminRole::handle();

    try {
        SyncRolePermissions::handle($role, new SyncRolePermissionsData(
            permissions: ['users.viewReports'],
        ));

        $this->fail('Expected protected super-admin permission exception was not thrown.');
    } catch (CannotRemoveRequiredSuperAdminPermissions $exception) {
        expect($exception->roleId)->toBe($role->id);
        expect($exception->roleName)->toBe('super-admin');
        expect($exception->permissionNames)->toBe(['users.viewReports']);
        expect($exception->requiredPermissionNames)->toBe([
            'users.manageMembers',
            'users.viewReports',
        ]);
        expect($exception->errors())->toBe([
            'permissions' => ['The protected super-admin role must keep every permission.'],
        ]);
        expect($exception->context()['role_id'])->toBe($role->id);
        expect($exception->context()['permission_names'])->toBe(['users.viewReports']);
        expect($exception->context()['required_permission_names'])->toBe([
            'users.manageMembers',
            'users.viewReports',
        ]);
        expect($exception->context()['error_fields'])->toBe(['permissions']);
    }
});

test('create permission action persists a permission', function (): void {
    $permission = CreatePermission::handle(new CreatePermissionData(
        name: 'users.manageMembers',
        label: 'Manage Members',
        description: 'Create, update, and remove member relationships.',
        group: 'users',
        groupLabel: 'User Administration',
        groupDescription: 'Identity, lifecycle, and role assignment controls.',
    ), app(PermissionGroupCatalogContract::class));

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

    $restoredPermission = CreatePermission::handle(new CreatePermissionData(
        name: 'users.manageMembers',
        label: 'Manage Team Members',
        description: 'Restore the permission with updated catalog copy.',
        group: 'users',
        groupLabel: 'User Administration',
        groupDescription: 'Identity, lifecycle, and role assignment controls.',
    ), app(PermissionGroupCatalogContract::class));

    expect($restoredPermission->id)->toBe($permission->id);
    expect($restoredPermission->trashed())->toBeFalse();
    expect($restoredPermission->label)->toBe('Manage Team Members');
});

test('create permission action re-syncs the protected super-admin role to the live permission catalog', function (): void {
    $role = EnsureSuperAdminRole::handle();
    $role->syncPermissions([]);

    $permission = CreatePermission::handle(new CreatePermissionData(
        name: 'users.manageMembers',
        label: 'Manage Members',
        description: 'Create, update, and remove member relationships.',
        group: 'users',
        groupLabel: 'User Administration',
        groupDescription: 'Identity, lifecycle, and role assignment controls.',
    ), app(PermissionGroupCatalogContract::class));

    expect($permission->exists)->toBeTrue();
    expect($role->fresh()?->permissions->pluck('name')->sort()->values()->all())
        ->toBe(Permission::query()->orderBy('name')->pluck('name')->all());
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

    UpdatePermission::handle($permission, new UpdatePermissionData(
        label: 'Manage Report Access',
        description: 'Review and maintain report visibility across teams.',
        group: 'roles',
        groupLabel: 'Role Management',
        groupDescription: 'Role creation, maintenance, and permission-footprint controls.',
    ), app(PermissionGroupCatalogContract::class));

    expect($permission->fresh()?->name)->toBe('users.viewReports');
    expect($permission->fresh()?->label)->toBe('Manage Report Access');
    expect($permission->fresh()?->description)->toBe('Review and maintain report visibility across teams.');
    expect($permission->fresh()?->group)->toBe('roles');
    expect($permission->fresh()?->permissionGroup?->label)->toBe('Role Management');
});

test('update permission action dispatches an auditable updated event', function (): void {
    Event::fake([PermissionUpdated::class]);

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

    UpdatePermission::handle($permission, new UpdatePermissionData(
        label: 'Manage Report Access',
        description: 'Review and maintain report visibility across teams.',
        group: 'roles',
        groupLabel: 'Role Management',
        groupDescription: 'Role creation, maintenance, and permission-footprint controls.',
    ), app(PermissionGroupCatalogContract::class));

    Event::assertDispatched(PermissionUpdated::class, fn (PermissionUpdated $event): bool => $event->auditEvent() === 'permissions.updated'
        && $event->auditSubjectId() === $permission->id);
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

    DeletePermission::handle($permission);

    expect(Permission::query()->find($permission->id))->toBeNull();
    expect(Permission::withTrashed()->find($permission->id)?->trashed())->toBeTrue();
});

test('delete permission action re-syncs the protected super-admin role to the remaining permission catalog', function (): void {
    $usersGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'users'],
        ['label' => 'Users'],
    );

    $role = EnsureSuperAdminRole::handle();

    Permission::query()->create([
        'name' => 'users.viewReports',
        'permission_group_id' => $usersGroup->id,
        'guard_name' => 'web',
    ]);

    $permission = Permission::query()->create([
        'name' => 'users.removeMembers',
        'permission_group_id' => $usersGroup->id,
        'guard_name' => 'web',
    ]);

    $role->syncPermissions([]);

    DeletePermission::handle($permission);

    expect($role->fresh()?->permissions->pluck('name')->sort()->values()->all())
        ->toBe(Permission::query()->orderBy('name')->pluck('name')->all());
});
