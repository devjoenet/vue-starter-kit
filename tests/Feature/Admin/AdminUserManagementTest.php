<?php

declare(strict_types=1);

use App\Modules\IAM\Actions\EnsureSuperAdminRole;
use App\Modules\IAM\Models\Role;
use App\Modules\Shared\Models\User;
use Database\Seeders\AdminAclSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->seed(AdminAclSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('super-admin');

    $this->actingAs($admin);
});

test('admin can update a user password when confirmation matches', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->put(route('admin.users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $response->assertSessionHasNoErrors();

    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});

test('admin cannot update a user password when confirmation does not match', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->from(route('admin.users.edit', $user))->put(route('admin.users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'new-password-123',
        'password_confirmation' => 'different-password-123',
    ]);

    $response->assertRedirect(route('admin.users.edit', $user));
    $response->assertSessionHasErrors(['password']);

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

test('quiet success user edit requests do not flash duplicate success messages', function (): void {
    $user = User::factory()->create();
    $role = Role::query()->create([
        'name' => 'support_specialist',
        'guard_name' => 'web',
    ]);

    $updateResponse = $this->from(route('admin.users.edit', $user))
        ->put(route('admin.users.update', [
            'user' => $user,
            'quiet_success' => 1,
        ]), [
            'name' => 'Updated User',
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
        ]);

    $updateResponse->assertRedirect(route('admin.users.edit', $user));
    $updateResponse->assertSessionMissing('success');

    $syncResponse = $this->from(route('admin.users.edit', $user))
        ->put(route('admin.users.roles.sync', [
            'user' => $user,
            'quiet_success' => 1,
        ]), [
            'roles' => [$role->name],
        ]);

    $syncResponse->assertRedirect(route('admin.users.edit', $user));
    $syncResponse->assertSessionMissing('success');

    expect($user->fresh()?->hasRole($role))->toBeTrue();
});

test('admin can recreate a soft deleted user email by restoring the existing record', function (): void {
    $user = User::factory()->create([
        'name' => 'Archived User',
        'email' => 'archived@example.test',
    ]);

    $user->delete();

    $response = $this->post(route('admin.users.store'), [
        'name' => 'Restored User',
        'email' => 'archived@example.test',
        'password' => 'new-password-123',
        'password_confirmation' => 'new-password-123',
    ]);

    $restoredUser = User::query()
        ->where('email', 'archived@example.test')
        ->first();

    expect($restoredUser)->not->toBeNull();
    expect($restoredUser?->id)->toBe($user->id);
    expect($restoredUser?->trashed())->toBeFalse();
    expect($restoredUser?->name)->toBe('Restored User');
    expect(Hash::check('new-password-123', $restoredUser->password))->toBeTrue();

    $response->assertRedirect(route('admin.users.edit', $restoredUser));
});

test('admin cannot delete the last super-admin user', function (): void {
    /** @var User $admin */
    $admin = auth()->user();

    $response = $this->from(route('admin.users.edit', $admin))
        ->delete(route('admin.users.destroy', $admin));

    $response->assertRedirect(route('admin.users.edit', $admin));
    $response->assertSessionHasErrors(['user']);

    expect($admin->fresh())->not->toBeNull();
    expect($admin->fresh()?->hasRole(EnsureSuperAdminRole::name()))->toBeTrue();
});

test('admin cannot remove the last super-admin role assignment from a user', function (): void {
    /** @var User $admin */
    $admin = auth()->user();

    $role = Role::query()->create([
        'name' => 'support_specialist',
        'guard_name' => 'web',
    ]);

    $response = $this->from(route('admin.users.edit', $admin))
        ->put(route('admin.users.roles.sync', $admin), [
            'roles' => [$role->name],
        ]);

    $response->assertRedirect(route('admin.users.edit', $admin));
    $response->assertSessionHasErrors(['roles']);

    expect($admin->fresh()?->hasRole(EnsureSuperAdminRole::name()))->toBeTrue();
    expect($admin->fresh()?->hasRole($role))->toBeFalse();
});
