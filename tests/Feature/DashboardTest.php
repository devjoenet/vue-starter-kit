<?php

declare(strict_types=1);

use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\PermissionGroup;
use App\Modules\Roles\Models\Role;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('admin.dashboard'));
});

test('authenticated users can visit the admin dashboard', function () {
    $user = User::factory()->create();
    User::factory()->count(2)->create();
    Role::query()->create([
        'name' => 'reviewer',
        'guard_name' => 'web',
    ]);
    $reportsGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'reports'],
        ['label' => 'Reports'],
    );
    Permission::query()->create([
        'name' => 'reports.view',
        'permission_group_id' => $reportsGroup->id,
        'guard_name' => 'web',
    ]);

    $this->actingAs($user);

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Dashboard')
            ->where('counts.users', 3)
            ->where('counts.roles', 1)
            ->where('counts.permissions', 1)
        );
});

test('dashboard route ownership stays explicit', function () {
    $routes = collect(Route::getRoutes()->getRoutes());

    expect($routes->where('action.as', 'dashboard'))->toHaveCount(1);
    expect($routes->where('action.as', 'admin.dashboard'))->toHaveCount(1);
    expect($routes->where('uri', 'dashboard'))->toHaveCount(1);
    expect($routes->where('uri', 'admin')->where('action.as', 'admin.dashboard'))->toHaveCount(1);
});
