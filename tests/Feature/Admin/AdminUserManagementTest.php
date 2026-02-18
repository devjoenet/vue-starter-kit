<?php

declare(strict_types=1);

use App\Models\User;
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
