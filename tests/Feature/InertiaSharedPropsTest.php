<?php

declare(strict_types=1);

use App\Enums\AdminPermission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Carbon\CarbonInterface;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\PermissionRegistrar;

test('login page includes shared flash props', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/Login')
            ->where('name', config('app.name'))
            ->where('auth', [
                'user' => null,
                'roles' => [],
                'permissions' => [],
            ])
            ->where('sidebarOpen', true)
            ->has('flash')
            ->where('flash.success', null)
            ->where('flash.error', null)
            ->where('flash.warning', null)
            ->where('flash.info', null)
        );
});

test('shared flash success is available to inertia pages', function () {
    $this->withSession(['success' => 'Everything saved.'])
        ->get(route('login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/Login')
            ->where('flash.success', 'Everything saved.')
        );
});

test('shared flash warning and info are available to inertia pages', function () {
    $this->withSession([
        'warning' => 'Double-check this action.',
        'info' => 'Background sync started.',
    ])
        ->get(route('login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/Login')
            ->where('flash.warning', 'Double-check this action.')
            ->where('flash.info', 'Background sync started.')
        );
});

test('authenticated inertia pages include shared auth props and sidebar cookie state', function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $permission = Permission::query()->create([
        'name' => AdminPermission::UsersView->value,
        'group' => 'users',
        'guard_name' => 'web',
    ]);

    $role = Role::query()->create([
        'name' => 'admin',
        'guard_name' => 'web',
    ]);
    $role->givePermissionTo($permission);

    $user = User::factory()->create([
        'name' => 'Taylor Otwell',
        'email' => 'taylor@example.com',
    ]);
    $user->assignRole($role);
    $emailVerifiedAt = $user->email_verified_at;

    $this->actingAs($user)
        ->withUnencryptedCookie('sidebar_state', 'false')
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Dashboard')
            ->where('name', config('app.name'))
            ->where('sidebarOpen', false)
            ->where('auth', [
                'user' => [
                    'id' => $user->id,
                    'name' => 'Taylor Otwell',
                    'email' => 'taylor@example.com',
                    'email_verified_at' => $emailVerifiedAt instanceof CarbonInterface
                        ? $emailVerifiedAt->toJSON()
                        : null,
                ],
                'roles' => ['admin'],
                'permissions' => [AdminPermission::UsersView->value],
            ])
        );
});
