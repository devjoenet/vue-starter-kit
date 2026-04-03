<?php

declare(strict_types=1);

use App\Modules\Roles\Models\Role;
use App\Modules\Users\Actions\CreateUser;
use App\Modules\Users\Actions\DeleteUser;
use App\Modules\Users\Actions\SyncUserRoles;
use App\Modules\Users\Actions\UpdateUser;
use App\Modules\Users\DTOs\CreateUserData;
use App\Modules\Users\DTOs\SyncUserRolesData;
use App\Modules\Users\DTOs\UpdateUserData;
use App\Modules\Users\Exceptions\UnknownRolesSelected;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Hash;

test('create user action persists a user with a hashed password', function (): void {
    $user = CreateUser::handle(new CreateUserData(
        name: 'Taylor Otwell',
        email: 'taylor@example.com',
        password: 'secret-password',
    ));

    expect($user->exists)->toBeTrue();
    expect($user->name)->toBe('Taylor Otwell');
    expect($user->email)->toBe('taylor@example.com');
    expect(Hash::check('secret-password', $user->password))->toBeTrue();
});

test('create user action restores a soft deleted user with the same email', function (): void {
    $user = User::factory()->create([
        'name' => 'Archived User',
        'email' => 'archived@example.com',
        'two_factor_secret' => encrypt('secret'),
        'two_factor_recovery_codes' => encrypt('["code-1"]'),
        'two_factor_confirmed_at' => now(),
    ]);

    $user->delete();

    $restoredUser = CreateUser::handle(new CreateUserData(
        name: 'Restored User',
        email: 'archived@example.com',
        password: 'restored-password',
    ));

    expect($restoredUser->id)->toBe($user->id);
    expect($restoredUser->trashed())->toBeFalse();
    expect($restoredUser->name)->toBe('Restored User');
    expect($restoredUser->email_verified_at)->toBeNull();
    expect($restoredUser->two_factor_secret)->toBeNull();
    expect($restoredUser->two_factor_recovery_codes)->toBeNull();
    expect($restoredUser->two_factor_confirmed_at)->toBeNull();
    expect(Hash::check('restored-password', $restoredUser->password))->toBeTrue();
});

test('update user action updates profile fields and password when provided', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    UpdateUser::handle($user, new UpdateUserData(
        name: 'Updated Name',
        email: 'updated@example.com',
        password: 'new-password',
    ));

    expect($user->fresh()?->name)->toBe('Updated Name');
    expect($user->fresh()?->email)->toBe('updated@example.com');
    expect(Hash::check('new-password', (string) $user->fresh()?->password))->toBeTrue();
});

test('delete user action soft deletes the user', function (): void {
    $user = User::factory()->create();

    DeleteUser::handle($user);

    expect(User::query()->find($user->id))->toBeNull();
    expect(User::withTrashed()->find($user->id)?->trashed())->toBeTrue();
});

test('sync user roles action syncs the selected roles by name', function (): void {
    $user = User::factory()->create();

    Role::query()->create([
        'name' => 'editor',
        'guard_name' => 'web',
    ]);

    Role::query()->create([
        'name' => 'reviewer',
        'guard_name' => 'web',
    ]);

    SyncUserRoles::handle($user, new SyncUserRolesData(
        roles: ['editor', 'editor', 'reviewer'],
    ));

    expect($user->fresh()?->getRoleNames()->all())->toBe(['editor', 'reviewer']);
});

test('sync user roles action throws a domain exception when roles are missing', function (): void {
    $user = User::factory()->create();

    try {
        SyncUserRoles::handle($user, new SyncUserRolesData(
            roles: ['missing-role'],
        ));

        $this->fail('Expected unknown roles exception was not thrown.');
    } catch (UnknownRolesSelected $exception) {
        expect($exception->roleNames)->toBe(['missing-role']);
        expect($exception->errors())->toBe([
            'roles' => ['One or more selected roles are invalid.'],
        ]);
    }
});
