<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\User;
use Database\Seeders\AdminAclSeeder;
use Spatie\Permission\Models\Role;

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

test('admin can create permissions', function () {
    $response = $this->post(route('admin.permissions.store'), [
        'name' => 'view reports',
        'group' => 'users',
    ]);

    $permission = Permission::query()->where('name', 'users.viewReports')->first();

    expect($permission)->not->toBeNull();

    $response->assertRedirect(route('admin.permissions.edit', $permission));
});

test('admin permission names are normalized to group.camelCase on create and update', function () {
    $createResponse = $this->post(route('admin.permissions.store'), [
        'name' => 'Invite Team Members',
        'group' => 'USERS',
    ]);

    $permission = Permission::query()->where('name', 'users.inviteTeamMembers')->first();

    expect($permission)->not->toBeNull();
    expect($permission?->group)->toBe('users');

    $createResponse->assertRedirect(route('admin.permissions.edit', $permission));

    $updateResponse = $this->put(route('admin.permissions.update', $permission), [
        'name' => 'roles.manage users',
        'group' => 'ROLES',
    ]);

    expect($permission->fresh()?->name)->toBe('roles.manageUsers');
    expect($permission->fresh()?->group)->toBe('roles');

    $updateResponse->assertRedirect();
});

test('admin can create permissions with a new custom group', function () {
    $response = $this->post(route('admin.permissions.store'), [
        'name' => 'Issue Refund',
        'group' => 'Billing Ops',
    ]);

    $permission = Permission::query()->where('name', 'billing_ops.issueRefund')->first();

    expect($permission)->not->toBeNull();
    expect($permission?->group)->toBe('billing_ops');

    $response->assertRedirect(route('admin.permissions.edit', $permission));
});
