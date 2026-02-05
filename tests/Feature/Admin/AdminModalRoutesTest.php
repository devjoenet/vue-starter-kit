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

test('users create route renders index modal', function () {
    $this->get(route('admin.users.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Users/Index')
            ->where('modal.mode', 'create')
        );
});

test('users edit route renders index modal', function () {
    $target = User::factory()->create();

    $this->get(route('admin.users.edit', $target))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Users/Index')
            ->where('modal.mode', 'edit')
            ->where('modal.user.id', $target->id)
        );
});

test('roles create route renders index modal', function () {
    $this->get(route('admin.roles.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Roles/Index')
            ->where('modal.mode', 'create')
        );
});

test('roles edit route renders index modal', function () {
    $role = Role::query()->create([
        'name' => 'editor',
        'guard_name' => 'web',
    ]);

    $this->get(route('admin.roles.edit', $role))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Roles/Index')
            ->where('modal.mode', 'edit')
            ->where('modal.role.id', $role->id)
        );
});

test('permissions create route renders index modal', function () {
    $this->get(route('admin.permissions.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Permissions/Index')
            ->where('modal.mode', 'create')
        );
});

test('permissions edit route renders index modal', function () {
    $permission = Permission::query()->create([
        'name' => 'custom.view',
        'group' => 'users',
        'guard_name' => 'web',
    ]);

    $this->get(route('admin.permissions.edit', $permission))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Permissions/Index')
            ->where('modal.mode', 'edit')
            ->where('modal.permission.id', $permission->id)
        );
});
