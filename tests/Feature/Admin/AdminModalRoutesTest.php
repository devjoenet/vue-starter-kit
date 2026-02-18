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
            ->component('admin/Users/Create')
        );
});

test('users index route includes roles for each user row', function () {
    $target = User::factory()->create();
    $target->assignRole('super-admin');

    $this->get(route('admin.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Users/Index')
            ->has('users.data')
            ->where('users.data.0.roles', fn ($roles) => collect($roles)->contains('super-admin'))
        );
});

test('users edit route renders edit page', function () {
    $target = User::factory()->create();

    $this->get(route('admin.users.edit', $target))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Users/Edit')
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
            ->component('admin/Roles/Create')
        );
});

test('roles index route includes user counts for each role row', function () {
    $role = Role::query()->create([
        'name' => 'reviewer',
        'guard_name' => 'web',
    ]);

    User::factory()->count(2)->create()->each(fn (User $user) => $user->assignRole($role));

    $this->get(route('admin.roles.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Roles/Index')
            ->has('roles')
            ->where('roles', fn ($roles) => collect($roles)->contains(fn ($entry) => $entry['name'] === 'reviewer' && $entry['users_count'] === 2))
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
            ->component('admin/Roles/Edit')
            ->where('roleId', $role->id)
            ->where('roleName', $role->name)
            ->has('permissionsByGroup')
            ->has('rolePermissions')
        );
});

test('permissions create route renders create page', function () {
    $this->get(route('admin.permissions.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Permissions/Create')
            ->has('groups')
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
            ->component('admin/Permissions/Edit')
            ->where('permission.id', $permission->id)
            ->where('permission.name', $permission->name)
            ->where('permission.group', $permission->group)
            ->has('groups')
        );
});
