<?php

declare(strict_types=1);

use App\Modules\IAM\Actions\CreateUser;
use App\Modules\IAM\Actions\DeleteUser;
use App\Modules\IAM\Actions\EnsureSuperAdminRole;
use App\Modules\IAM\Actions\SyncUserRoles;
use App\Modules\IAM\Actions\UpdateUser;
use App\Modules\IAM\DTOs\CreateUserData;
use App\Modules\IAM\DTOs\SyncUserRolesData;
use App\Modules\IAM\DTOs\UpdateUserData;
use App\Modules\IAM\Events\UserCreated;
use App\Modules\IAM\Events\UserRolesSynced;
use App\Modules\IAM\Exceptions\CannotDeleteLastSuperAdminUser;
use App\Modules\IAM\Exceptions\CannotRemoveLastSuperAdminRoleAssignment;
use App\Modules\IAM\Exceptions\UnknownRolesSelected;
use App\Modules\IAM\Models\Role;
use App\Modules\Shared\Models\User;
use Illuminate\Support\Facades\Event;
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

test('create user action dispatches an auditable created event', function (): void {
    Event::fake([UserCreated::class]);

    $user = CreateUser::handle(new CreateUserData(
        name: 'Taylor Otwell',
        email: 'taylor.created@example.com',
        password: 'secret-password',
    ));

    Event::assertDispatched(UserCreated::class, fn (UserCreated $event): bool => $event->auditEvent() === 'users.created'
        && $event->auditSubjectId() === $user->id
        && $event->auditSubjectLabel() === 'taylor.created@example.com');
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

test('delete user action blocks deleting the last super-admin user', function (): void {
    $user = User::factory()->create();
    $user->assignRole(EnsureSuperAdminRole::handle());

    try {
        DeleteUser::handle($user);

        $this->fail('Expected last super-admin user exception was not thrown.');
    } catch (CannotDeleteLastSuperAdminUser $exception) {
        expect($exception->userId)->toBe($user->id);
        expect($exception->email)->toBe($user->email);
        expect($exception->errors())->toBe([
            'user' => ['The last active super-admin user cannot be deleted.'],
        ]);
        expect($exception->context()['user_id'])->toBe($user->id);
        expect($exception->context()['email'])->toBe($user->email);
        expect($exception->context()['protected_role'])->toBe('super-admin');
        expect($exception->context()['error_fields'])->toBe(['user']);
    }
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

test('sync user roles action dispatches an auditable sync event', function (): void {
    Event::fake([UserRolesSynced::class]);

    $user = User::factory()->create();

    Role::query()->create([
        'name' => 'editor',
        'guard_name' => 'web',
    ]);

    SyncUserRoles::handle($user, new SyncUserRolesData(
        roles: ['editor'],
    ));

    Event::assertDispatched(UserRolesSynced::class, fn (UserRolesSynced $event): bool => $event->auditEvent() === 'users.roles_synced'
        && $event->auditSubjectId() === $user->id);
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
        expect($exception->context()['role_names'])->toBe(['missing-role']);
        expect($exception->context()['error_fields'])->toBe(['roles']);
    }
});

test('sync user roles action blocks removing the last super-admin role assignment', function (): void {
    $user = User::factory()->create();
    $user->assignRole(EnsureSuperAdminRole::handle());

    Role::query()->create([
        'name' => 'reviewer',
        'guard_name' => 'web',
    ]);

    try {
        SyncUserRoles::handle($user, new SyncUserRolesData(
            roles: ['reviewer'],
        ));

        $this->fail('Expected last super-admin role assignment exception was not thrown.');
    } catch (CannotRemoveLastSuperAdminRoleAssignment $exception) {
        expect($exception->userId)->toBe($user->id);
        expect($exception->email)->toBe($user->email);
        expect($exception->nextRoleNames)->toBe(['reviewer']);
        expect($exception->errors())->toBe([
            'roles' => ['The last active super-admin user must keep the super-admin role.'],
        ]);
        expect($exception->context()['user_id'])->toBe($user->id);
        expect($exception->context()['email'])->toBe($user->email);
        expect($exception->context()['next_role_names'])->toBe(['reviewer']);
        expect($exception->context()['protected_role'])->toBe('super-admin');
        expect($exception->context()['error_fields'])->toBe(['roles']);
    }
});
