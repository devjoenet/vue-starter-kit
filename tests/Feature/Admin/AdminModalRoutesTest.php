<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\User;
use Database\Seeders\AdminAclSeeder;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(AdminAclSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $this->actingAs($user);
});

test('users create route renders create page', function () {
    $this->get(route('admin.users.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Users/Create')
        );
});

test('users edit route renders edit page', function () {
    $target = User::factory()->create();

    $this->get(route('admin.users.edit', $target))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Users/Edit')
            ->where('user.id', $target->id)
            ->where('user.name', $target->name)
            ->where('user.email', $target->email)
            ->has('roles')
            ->has('userRoles')
        );
});

test('roles create route renders create page', function () {
    $this->get(route('admin.roles.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Roles/Create')
        );
});

test('roles edit route renders edit page', function () {
    $role = Role::query()->create([
        'name' => 'editor',
        'guard_name' => 'web',
    ]);

    $this->get(route('admin.roles.edit', $role))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Roles/Edit')
            ->where('role.id', $role->id)
            ->where('role.name', $role->name)
            ->has('permissionsByGroup')
            ->has('rolePermissions')
        );
});

test('permissions create route renders create page', function () {
    $this->get(route('admin.permissions.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Permissions/Create')
        );
});

test('permissions edit route renders edit page', function () {
    $permission = Permission::query()->create([
        'name' => 'custom.view',
        'group' => 'users',
        'guard_name' => 'web',
    ]);

    $this->get(route('admin.permissions.edit', $permission))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Permissions/Edit')
            ->where('permission.id', $permission->id)
            ->where('permission.name', $permission->name)
            ->where('permission.group', $permission->group)
        );
});
