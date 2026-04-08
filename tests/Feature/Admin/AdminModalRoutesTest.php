<?php

declare(strict_types=1);

use App\Modules\IAM\Models\Permission;
use App\Modules\IAM\Models\PermissionGroup;
use App\Modules\IAM\Models\Role;
use App\Modules\Shared\Models\User;
use Database\Seeders\AdminAclSeeder;
use Inertia\Testing\AssertableInertia as Assert;

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
            ->has('filterOptions.roles')
            ->where('query.sort', 'id')
            ->where('query.direction', 'asc')
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
            ->has('userRoles')
            ->missing('roles')
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->has('roles')
                ->missing('user')
                ->missing('userRoles')
            )
        );
});

test('users edit route supports partial reloads for user edit state', function () {
    /** @var User $target */
    $target = auth()->user();

    $target->assignRole('super-admin');

    $this->get(route('admin.users.edit', $target))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Users/Edit')
            ->reloadOnly(['user', 'auth', 'flash'], fn (Assert $reload) => $reload
                ->has('user')
                ->has('auth.user')
                ->has('flash')
                ->missing('roles')
                ->missing('userRoles')
            )
            ->reloadOnly(['userRoles', 'flash'], fn (Assert $reload) => $reload
                ->has('userRoles')
                ->has('flash')
                ->missing('user')
                ->missing('roles')
            )
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
            ->has('filterOptions.users')
            ->where('query.sort', 'id')
            ->where('query.direction', 'asc')
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
            ->where('role.id', $role->id)
            ->where('role.name', $role->name)
            ->has('rolePermissions')
            ->missing('permissionsByGroup')
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->has('permissionsByGroup')
                ->missing('role')
                ->missing('rolePermissions')
            )
        );
});

test('roles edit route supports partial reloads for role edit state', function () {
    $role = Role::query()->create([
        'name' => 'editor',
        'guard_name' => 'web',
    ]);

    $this->get(route('admin.roles.edit', $role))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Roles/Edit')
            ->reloadOnly(['role', 'flash'], fn (Assert $reload) => $reload
                ->has('role')
                ->has('flash')
                ->missing('permissionsByGroup')
                ->missing('rolePermissions')
            )
            ->reloadOnly(['rolePermissions', 'flash'], fn (Assert $reload) => $reload
                ->has('rolePermissions')
                ->has('flash')
                ->missing('role')
                ->missing('permissionsByGroup')
            )
        );
});

test('permissions create route renders create page', function () {
    $this->get(route('admin.permissions.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Permissions/Create')
            ->has('groups')
            ->has('groups.0.slug')
            ->has('groups.0.label')
        );
});

test('permissions index route supports partial reloads for permission table state', function () {
    $this->get(route('admin.permissions.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Permissions/Index')
            ->has('permissions')
            ->has('groups')
            ->has('filterOptions.group')
            ->where('query.sort', 'id')
            ->where('query.direction', 'asc')
            ->reloadOnly(['permissions', 'groups', 'filterOptions', 'query'], fn (Assert $reload) => $reload
                ->has('permissions')
                ->has('groups')
                ->has('filterOptions.group')
                ->has('query')
            )
        );
});

test('permissions edit route renders edit page', function () {
    $usersGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'users'],
        ['label' => 'Users'],
    );

    $permission = Permission::query()->create([
        'name' => 'custom.view',
        'label' => 'View Custom Records',
        'permission_group_id' => $usersGroup->id,
        'guard_name' => 'web',
    ]);

    $this->get(route('admin.permissions.edit', $permission))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Permissions/Edit')
            ->where('permission.id', $permission->id)
            ->where('permission.name', $permission->name)
            ->where('permission.label', 'View Custom Records')
            ->where('permission.group', $permission->group)
            ->where('permission.group_label', 'User Administration')
            ->has('groups')
        );
});
