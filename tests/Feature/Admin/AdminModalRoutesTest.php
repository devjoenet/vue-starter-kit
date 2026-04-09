<?php

declare(strict_types=1);

use App\Modules\Audit\Models\AuditLog;
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
    AuditLog::query()->create([
        'event' => 'users.updated',
        'subject_type' => User::class,
        'subject_id' => $target->id,
        'subject_label' => $target->email,
        'summary' => 'Updated user audit history.',
        'changes' => [
            'before' => ['email' => 'previous@example.test'],
            'after' => ['email' => $target->email],
        ],
    ]);

    $this->get(route('admin.users.edit', $target))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Users/Edit')
            ->where('user.id', $target->id)
            ->where('user.name', $target->name)
            ->where('user.email', $target->email)
            ->has('userRoles')
            ->missing('roles')
            ->missing('auditHistory')
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->has('roles')
                ->where('auditHistory.0.event', 'users.updated')
                ->where('auditHistory.0.changes.0.field', 'email')
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
                ->missing('auditHistory')
                ->missing('userRoles')
            )
            ->reloadOnly(['userRoles', 'flash'], fn (Assert $reload) => $reload
                ->has('userRoles')
                ->has('flash')
                ->missing('user')
                ->missing('auditHistory')
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
    AuditLog::query()->create([
        'event' => 'roles.updated',
        'subject_type' => Role::class,
        'subject_id' => $role->id,
        'subject_label' => $role->name,
        'summary' => 'Updated role audit history.',
        'changes' => [
            'before' => ['name' => 'reviewer'],
            'after' => ['name' => $role->name],
        ],
    ]);

    $this->get(route('admin.roles.edit', $role))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Roles/Edit')
            ->where('role.id', $role->id)
            ->where('role.name', $role->name)
            ->has('rolePermissions')
            ->missing('permissionsByGroup')
            ->missing('auditHistory')
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->has('permissionsByGroup')
                ->where('auditHistory.0.event', 'roles.updated')
                ->where('auditHistory.0.changes.0.field', 'name')
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
                ->missing('auditHistory')
                ->missing('rolePermissions')
            )
            ->reloadOnly(['rolePermissions', 'flash'], fn (Assert $reload) => $reload
                ->has('rolePermissions')
                ->has('flash')
                ->missing('role')
                ->missing('auditHistory')
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
    AuditLog::query()->create([
        'event' => 'permissions.updated',
        'subject_type' => Permission::class,
        'subject_id' => $permission->id,
        'subject_label' => $permission->name,
        'summary' => 'Updated permission audit history.',
        'changes' => [
            'before' => ['label' => 'View Records'],
            'after' => ['label' => $permission->label],
        ],
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
            ->missing('auditHistory')
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->where('auditHistory.0.event', 'permissions.updated')
                ->where('auditHistory.0.changes.0.field', 'label')
                ->missing('permission')
                ->missing('groups')
            )
        );
});
