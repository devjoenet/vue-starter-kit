<?php

use App\Models\Permission;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\PermissionRegistrar;

test('auth permissions are shared with inertia', function () {
    $user = User::factory()->create();
    $permission = Permission::query()->create([
        'name' => 'permissions.view',
        'guard_name' => 'web',
        'group' => 'permissions',
    ]);

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $user->givePermissionTo($permission);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('auth.permissions', [$permission->name])
        );
});
