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

test('admin can create permissions', function () {
    $response = $this->post(route('admin.permissions.store'), [
        'name' => 'support.view',
        'group' => 'users',
    ]);

    $permission = Permission::query()->where('name', 'support.view')->first();

    expect($permission)->not->toBeNull();

    $response->assertRedirect(route('admin.permissions.edit', $permission));
});
