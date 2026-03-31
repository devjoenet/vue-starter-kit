<?php

declare(strict_types=1);

use App\Modules\Settings\Actions\DeleteProfile;
use App\Modules\Settings\Actions\UpdatePassword;
use App\Modules\Settings\Actions\UpdateProfile;
use App\Modules\Settings\DTOs\UpdateProfileData;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Hash;

test('update profile action updates user details and clears verification when email changes', function (): void {
    $user = User::factory()->create();

    UpdateProfile::handle($user, new UpdateProfileData(
        name: 'Updated User',
        email: 'updated@example.com',
    ));

    expect($user->fresh()?->name)->toBe('Updated User');
    expect($user->fresh()?->email)->toBe('updated@example.com');
    expect($user->fresh()?->email_verified_at)->toBeNull();
});

test('update password action hashes the new password', function (): void {
    $user = User::factory()->create();

    UpdatePassword::handle($user, 'new-password');

    expect(Hash::check('new-password', (string) $user->fresh()?->password))->toBeTrue();
});

test('delete profile action removes the user account', function (): void {
    $user = User::factory()->create();

    DeleteProfile::handle($user);

    expect(User::query()->find($user->id))->toBeNull();
});
