<?php

declare(strict_types=1);

use App\Modules\Permissions\Models\Permission;
use App\Modules\Roles\Models\Role;
use App\Modules\Users\Models\User;
use Database\Seeders\AdminAclSeeder;
use Database\Seeders\PermissionGroupsSeeder;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

it('creates a user through the interactive console command', function () {
    /** @var TestCase $this */
    $this->seed(AdminAclSeeder::class);

    $this->artisan('create:user')
        ->expectsPromptsIntro('Create an admin user')
        ->expectsQuestion('Name', 'Console User')
        ->expectsQuestion('Email address', 'console@example.com')
        ->expectsQuestion('Password', 'password123')
        ->expectsQuestion('Confirm password', 'password123')
        ->expectsConfirmation('No active super-admin users exist. Should this user be the designated super-admin?', 'yes')
        ->expectsConfirmation('Create this user?', 'yes')
        ->expectsPromptsOutro('User created.')
        ->assertSuccessful();

    $user = User::query()
        ->where('email', 'console@example.com')
        ->firstOrFail();

    expect($user->name)->toBe('Console User');
    expect(Hash::check('password123', $user->password))->toBeTrue();
    expect($user->hasRole('super-admin'))->toBeTrue();
});

it('does not prompt for super-admin designation when one already exists', function () {
    /** @var TestCase $this */
    $this->seed(AdminAclSeeder::class);

    $existingSuperAdmin = User::factory()->create([
        'name' => 'Existing Super Admin',
        'email' => 'superadmin@example.com',
    ]);
    $existingSuperAdmin->assignRole('super-admin');

    $this->artisan('create:user')
        ->expectsPromptsIntro('Create an admin user')
        ->expectsQuestion('Name', 'Console User')
        ->expectsQuestion('Email address', 'console@example.com')
        ->expectsQuestion('Password', 'password123')
        ->expectsQuestion('Confirm password', 'password123')
        ->expectsConfirmation('Create this user?', 'yes')
        ->expectsPromptsOutro('User created.')
        ->assertSuccessful();

    $user = User::query()
        ->where('email', 'console@example.com')
        ->firstOrFail();

    expect($user->hasRole('super-admin'))->toBeFalse();
});

it('restores and re-syncs the super-admin role before assigning the first console admin user', function () {
    /** @var TestCase $this */
    $this->seed(AdminAclSeeder::class);

    $superAdminRole = Role::query()
        ->where('name', 'super-admin')
        ->firstOrFail();
    $superAdminRole->syncPermissions([]);
    $superAdminRole->delete();

    $this->artisan('create:user')
        ->expectsPromptsIntro('Create an admin user')
        ->expectsQuestion('Name', 'Restored Console User')
        ->expectsQuestion('Email address', 'restored-console@example.com')
        ->expectsQuestion('Password', 'password123')
        ->expectsQuestion('Confirm password', 'password123')
        ->expectsConfirmation('No active super-admin users exist. Should this user be the designated super-admin?', 'yes')
        ->expectsConfirmation('Create this user?', 'yes')
        ->expectsPromptsOutro('User created.')
        ->assertSuccessful();

    $user = User::query()
        ->where('email', 'restored-console@example.com')
        ->firstOrFail();
    $restoredRole = Role::query()
        ->where('name', 'super-admin')
        ->firstOrFail();

    expect($restoredRole->trashed())->toBeFalse();
    expect($user->hasRole($restoredRole))->toBeTrue();
    expect($restoredRole->permissions()->pluck('name')->sort()->values()->all())
        ->toEqual(Permission::query()->orderBy('name')->pluck('name')->all());
});

it('creates a role through the interactive console command', function () {
    /** @var TestCase $this */
    $user = User::factory()->create([
        'name' => 'Console Member',
        'email' => 'member@example.com',
    ]);

    $userOptionLabel = sprintf('%s <%s>', $user->name, $user->email);

    $this->artisan('create:role')
        ->expectsPromptsIntro('Create a role')
        ->expectsQuestion('Role name', 'Support Manager')
        ->expectsConfirmation('Assign existing users to this role?', 'yes')
        ->expectsChoice(
            'Users to assign to this role',
            [$user->id],
            [$user->id => $userOptionLabel],
        )
        ->expectsConfirmation('Create this role?', 'yes')
        ->expectsPromptsOutro('Role created.')
        ->assertSuccessful();

    $role = Role::query()
        ->where('name', 'support-manager')
        ->firstOrFail();

    expect($user->fresh()->hasRole($role))->toBeTrue();
});

it('creates a permission through the interactive console command', function () {
    /** @var TestCase $this */
    $this->seed(PermissionGroupsSeeder::class);

    $this->artisan('create:permission')
        ->expectsPromptsIntro('Create a permission')
        ->expectsQuestion('Permission group', 'Users')
        ->expectsQuestion('Group label', 'User Administration')
        ->expectsQuestion(
            'Group description (optional)',
            'Identity, lifecycle, and role-assignment access for internal users.',
        )
        ->expectsQuestion('Permission key or action', 'Create')
        ->expectsQuestion('Permission label', 'Create')
        ->expectsQuestion(
            'Permission description (optional)',
            'Create staff users and internal operator accounts.',
        )
        ->expectsConfirmation('Create this permission?', 'yes')
        ->expectsPromptsOutro('Permission created.')
        ->assertSuccessful();

    $permission = Permission::query()
        ->where('name', 'users.create')
        ->firstOrFail();

    expect($permission->group)->toBe('users');
    expect($permission->label)->toBe('Create');
    expect($permission->description)->toBe('Create staff users and internal operator accounts.');
});
