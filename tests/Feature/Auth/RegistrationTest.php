<?php

declare(strict_types=1);

use App\Modules\Shared\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration restores a soft deleted user with the same email', function () {
    // Arrange

    $user = User::factory()->create([
        'name' => 'Archived User',
        'email' => 'archived@example.com',
        'two_factor_secret' => encrypt('secret'),
        'two_factor_recovery_codes' => encrypt('["code-1"]'),
        'two_factor_confirmed_at' => now(),
    ]);

    $user->delete();

    // Act

    $response = $this->post(route('register.store'), [
        'name' => 'Restored User',
        'email' => 'archived@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Assert

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $restoredUser = User::query()
        ->where('email', 'archived@example.com')
        ->firstOrFail();

    expect($restoredUser->id)->toBe($user->id);
    expect($restoredUser->trashed())->toBeFalse();
    expect($restoredUser->name)->toBe('Restored User');
    expect($restoredUser->email_verified_at)->toBeNull();
    expect($restoredUser->two_factor_secret)->toBeNull();
    expect($restoredUser->two_factor_recovery_codes)->toBeNull();
    expect($restoredUser->two_factor_confirmed_at)->toBeNull();
});
