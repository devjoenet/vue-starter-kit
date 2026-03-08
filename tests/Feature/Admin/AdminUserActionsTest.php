<?php

declare(strict_types=1);

use App\Actions\Admin\Users\CreateUser;
use App\Actions\Admin\Users\DeleteUser;
use App\Actions\Admin\Users\SyncUserRoles;
use App\Actions\Admin\Users\UpdateUser;
use App\Models\User;
use App\Support\Data\Admin\Users\CreateUserData;
use App\Support\Data\Admin\Users\SyncUserRolesData;
use App\Support\Data\Admin\Users\UpdateUserData;
use App\Support\Exceptions\UnknownRolesSelected;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

test('create user action persists a user with a hashed password', function (): void {
    $user = app(CreateUser::class)->handle(new CreateUserData(
        name: 'Taylor Otwell',
        email: 'taylor@example.com',
        password: 'secret-password',
    ));

    expect($user->exists)->toBeTrue();
    expect($user->name)->toBe('Taylor Otwell');
    expect($user->email)->toBe('taylor@example.com');
    expect(Hash::check('secret-password', $user->password))->toBeTrue();
});

test('update user action updates profile fields and password when provided', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    app(UpdateUser::class)->handle($user, new UpdateUserData(
        name: 'Updated Name',
        email: 'updated@example.com',
        password: 'new-password',
    ));

    expect($user->fresh()?->name)->toBe('Updated Name');
    expect($user->fresh()?->email)->toBe('updated@example.com');
    expect(Hash::check('new-password', (string) $user->fresh()?->password))->toBeTrue();
});

test('delete user action removes the user', function (): void {
    $user = User::factory()->create();

    app(DeleteUser::class)->handle($user);

    expect(User::query()->find($user->id))->toBeNull();
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

    app(SyncUserRoles::class)->handle($user, new SyncUserRolesData(
        roles: ['editor', 'reviewer'],
    ));

    expect($user->fresh()?->getRoleNames()->all())->toBe(['editor', 'reviewer']);
});

test('sync user roles action throws a domain exception when roles are missing', function (): void {
    $user = User::factory()->create();

    try {
        app(SyncUserRoles::class)->handle($user, new SyncUserRolesData(
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
