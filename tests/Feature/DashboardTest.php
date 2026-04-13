<?php

declare(strict_types=1);

use App\Modules\IAM\Permissions\Models\Permission;
use App\Modules\IAM\Permissions\Models\PermissionGroup;
use App\Modules\IAM\Roles\Models\Role;
use App\Modules\Shared\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    // Act

    $response = $this->get(route('dashboard'));

    // Assert

    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    // Arrange

    $user = User::factory()->create();
    $this->actingAs($user);

    // Act

    $response = $this->get(route('dashboard'));

    // Assert

    $response->assertRedirect(route('admin.dashboard'));
});

test('authenticated users can visit the admin dashboard', function () {
    // Arrange

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

    // Act

    $response = $this->get(route('admin.dashboard'));

    // Assert

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Dashboard')
            ->where('sources.overview.users', 3)
            ->where('sources.overview.roles', 1)
            ->where('sources.overview.permissions', 1)
            ->where('sources.users.count', 3)
            ->where('sources.roles.count', 1)
            ->where('sources.permissions.count', 1)
        );
});

test('dashboard route ownership stays explicit', function () {
    // Arrange

    $routes = collect(Route::getRoutes()->getRoutes());

    // Assert

    expect($routes->where('action.as', 'dashboard'))->toHaveCount(1);
    expect($routes->where('action.as', 'admin.dashboard'))->toHaveCount(1);
    expect($routes->where('uri', 'dashboard'))->toHaveCount(1);
    expect($routes->where('uri', 'admin')->where('action.as', 'admin.dashboard'))->toHaveCount(1);
});
