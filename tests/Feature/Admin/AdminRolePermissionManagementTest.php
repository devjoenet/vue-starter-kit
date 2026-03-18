<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdminAclSeeder;

beforeEach(function () {
    $this->seed(AdminAclSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $this->actingAs($user);
});

test('admin can create roles', function () {
    $response = $this->post(route('admin.roles.store'), [
        'name' => 'support',
    ]);

    $role = Role::query()->where('name', 'support')->first();

    expect($role)->not->toBeNull();

    $response->assertRedirect(route('admin.roles.edit', $role));
});

test('admin role names are normalized to kebab-case on create and update', function () {
    $createResponse = $this->post(route('admin.roles.store'), [
        'name' => 'Support Team Lead',
    ]);

    $role = Role::query()->where('name', 'support-team-lead')->first();

    expect($role)->not->toBeNull();

    $createResponse->assertRedirect(route('admin.roles.edit', $role));

    $updateResponse = $this->put(route('admin.roles.update', $role), [
        'name' => 'SupportManager',
    ]);

    expect($role->fresh()?->name)->toBe('support-manager');

    $updateResponse->assertRedirect();
});

test('admin can create roles and assign users', function () {
    $assignableUsers = User::factory()->count(2)->create();

    $response = $this->post(route('admin.roles.store'), [
        'name' => 'auditor',
        'user_ids' => $assignableUsers->pluck('id')->all(),
    ]);

    $role = Role::query()->where('name', 'auditor')->first();

    expect($role)->not->toBeNull();
    expect($assignableUsers[0]->fresh()->hasRole('auditor'))->toBeTrue();
    expect($assignableUsers[1]->fresh()->hasRole('auditor'))->toBeTrue();

    $response->assertRedirect(route('admin.roles.edit', $role));
});

test('admin can recreate a soft deleted role name by restoring the existing record', function () {
    $role = Role::query()->create([
        'name' => 'auditor',
        'guard_name' => 'web',
    ]);

    $role->delete();

    $response = $this->post(route('admin.roles.store'), [
        'name' => 'auditor',
    ]);

    $restoredRole = Role::query()->where('name', 'auditor')->first();

    expect($restoredRole)->not->toBeNull();
    expect($restoredRole?->id)->toBe($role->id);
    expect($restoredRole?->trashed())->toBeFalse();

    $response->assertRedirect(route('admin.roles.edit', $restoredRole));
});

test('admin can create permissions', function () {
    $response = $this->post(route('admin.permissions.store'), [
        'name' => 'view reports',
        'group' => 'users',
    ]);

    $permission = Permission::query()->where('name', 'users.viewReports')->first();

    expect($permission)->not->toBeNull();
    expect($permission?->label)->toBe('View Reports');
    expect($permission?->description)->toBeNull();
    expect($permission?->group)->toBe('users');

    $response->assertRedirect(route('admin.permissions.edit', $permission));
});

test('admin can recreate a soft deleted permission key by restoring the existing record', function () {
    $usersGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'users'],
        ['label' => 'Users'],
    );

    $permission = Permission::query()->create([
        'name' => 'users.exportData',
        'label' => 'Export Data',
        'permission_group_id' => $usersGroup->id,
        'guard_name' => 'web',
    ]);

    $permission->delete();

    $response = $this->post(route('admin.permissions.store'), [
        'name' => 'users.exportData',
        'label' => 'Export Reports',
        'description' => 'Reactivated export access for reporting workflows.',
        'group' => 'users',
        'group_label' => 'User Administration',
        'group_description' => 'Identity, lifecycle, and role-assignment access for internal users.',
    ]);

    $restoredPermission = Permission::query()->where('name', 'users.exportData')->first();

    expect($restoredPermission)->not->toBeNull();
    expect($restoredPermission?->id)->toBe($permission->id);
    expect($restoredPermission?->trashed())->toBeFalse();
    expect($restoredPermission?->label)->toBe('Export Reports');

    $response->assertRedirect(route('admin.permissions.edit', $restoredPermission));
});

test('admin permission keys are normalized on create while updates keep the key immutable', function () {
    $createResponse = $this->post(route('admin.permissions.store'), [
        'name' => 'Invite Team Members',
        'group' => 'USERS',
    ]);

    $permission = Permission::query()->where('name', 'users.inviteTeamMembers')->first();

    expect($permission)->not->toBeNull();
    expect($permission?->group)->toBe('users');
    expect($permission?->label)->toBe('Invite Team Members');

    $createResponse->assertRedirect(route('admin.permissions.edit', $permission));

    $updateResponse = $this->put(route('admin.permissions.update', $permission), [
        'name' => 'users.inviteTeamMembers',
        'label' => 'Manage Team Invitations',
        'description' => 'Approve or revoke invitations for shared workspace members.',
        'group' => 'ROLES',
        'group_label' => 'Role Operations',
        'group_description' => 'Catalog permissions that govern role design and assignment work.',
    ]);

    expect($permission->fresh()?->name)->toBe('users.inviteTeamMembers');
    expect($permission->fresh()?->label)->toBe('Manage Team Invitations');
    expect($permission->fresh()?->description)->toBe('Approve or revoke invitations for shared workspace members.');
    expect($permission->fresh()?->group)->toBe('roles');
    expect($permission->fresh()?->permissionGroup?->label)->toBe('Role Operations');

    $updateResponse->assertRedirect();
});

test('admin can create permissions with a new custom group', function () {
    $response = $this->post(route('admin.permissions.store'), [
        'name' => 'Issue Refund',
        'group' => 'Billing Ops',
        'group_label' => 'Billing Operations',
        'group_description' => 'Refunds, credits, and ledger-side customer billing access.',
        'description' => 'Authorize refunds and billing corrections for customer accounts.',
    ]);

    $permission = Permission::query()->where('name', 'billing_ops.issueRefund')->first();

    expect($permission)->not->toBeNull();
    expect($permission?->group)->toBe('billing_ops');
    expect($permission?->label)->toBe('Issue Refund');
    expect($permission?->description)->toBe('Authorize refunds and billing corrections for customer accounts.');
    expect($permission?->permissionGroup?->label)->toBe('Billing Operations');
    expect($permission?->permissionGroup?->description)->toBe('Refunds, credits, and ledger-side customer billing access.');

    $response->assertRedirect(route('admin.permissions.edit', $permission));
});

test('admin receives a validation error when attempting to change a permission key', function () {
    $usersGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'users'],
        ['label' => 'Users'],
    );

    $permission = Permission::query()->create([
        'name' => 'users.exportData',
        'label' => 'Export Data',
        'permission_group_id' => $usersGroup->id,
        'guard_name' => 'web',
    ]);

    $response = $this->from(route('admin.permissions.edit', $permission))
        ->put(route('admin.permissions.update', $permission), [
            'name' => 'roles.manageUsers',
            'label' => 'Manage Users',
            'description' => 'Attempting to rename the key should fail.',
            'group' => 'roles',
            'group_label' => 'Role Management',
            'group_description' => 'Role creation, maintenance, and permission-footprint controls.',
        ]);

    $response->assertRedirect(route('admin.permissions.edit', $permission));
    $response->assertSessionHasErrors(['name']);

    expect($permission->fresh()?->name)->toBe('users.exportData');
});

test('admin sees a validation error when syncing missing role permissions', function () {
    $role = Role::query()->create([
        'name' => 'reviewer',
        'guard_name' => 'web',
    ]);

    $response = $this->from(route('admin.roles.edit', $role))
        ->put(route('admin.roles.permissions.sync', $role), [
            'permissions' => ['users.missingPermission'],
        ]);

    $response->assertRedirect(route('admin.roles.edit', $role));
    $response->assertSessionHasErrors(['permissions']);

    expect($role->fresh()?->permissions)->toHaveCount(0);
});

test('quiet success role edit requests do not flash duplicate success messages', function () {
    $role = Role::query()->create([
        'name' => 'qa_lead',
        'guard_name' => 'web',
    ]);
    $qaLeadGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'qa_lead'],
        ['label' => 'Qa Lead'],
    );

    $permission = Permission::query()->create([
        'name' => 'qa_lead.approveRelease',
        'permission_group_id' => $qaLeadGroup->id,
        'guard_name' => 'web',
    ]);

    $updateResponse = $this->from(route('admin.roles.edit', $role))
        ->put(route('admin.roles.update', [
            'role' => $role,
            'quiet_success' => 1,
        ]), [
            'name' => 'qa-team-lead',
        ]);

    $updateResponse->assertRedirect(route('admin.roles.edit', $role));
    $updateResponse->assertSessionMissing('success');

    $syncResponse = $this->from(route('admin.roles.edit', $role))
        ->put(route('admin.roles.permissions.sync', [
            'role' => $role->fresh(),
            'quiet_success' => 1,
        ]), [
            'permissions' => [$permission->name],
        ]);

    $syncResponse->assertRedirect(route('admin.roles.edit', $role->fresh()));
    $syncResponse->assertSessionMissing('success');

    expect($role->fresh()?->hasPermissionTo($permission))->toBeTrue();
});

test('quiet success permission edit requests do not flash duplicate success messages', function () {
    $usersGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'users'],
        ['label' => 'Users'],
    );

    $permission = Permission::query()->create([
        'name' => 'users.exportData',
        'label' => 'Export Data',
        'permission_group_id' => $usersGroup->id,
        'guard_name' => 'web',
    ]);

    $response = $this->from(route('admin.permissions.edit', $permission))
        ->put(route('admin.permissions.update', [
            'permission' => $permission,
            'quiet_success' => 1,
        ]), [
            'name' => 'users.exportData',
            'label' => 'Export Reports',
            'description' => 'Download user-facing reports for audits or handoffs.',
            'group' => 'users',
            'group_label' => 'User Administration',
            'group_description' => 'Identity, lifecycle, and role-assignment access for internal users.',
        ]);

    $response->assertRedirect(route('admin.permissions.edit', $permission));
    $response->assertSessionMissing('success');

    expect($permission->fresh()?->name)->toBe('users.exportData');
    expect($permission->fresh()?->label)->toBe('Export Reports');
});
