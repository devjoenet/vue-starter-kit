<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\User;
use Modules\Settings\Actions\DeleteProfile;
use Modules\Settings\Actions\UpdatePassword;
use Modules\Settings\Actions\UpdateProfile;
use Modules\Settings\DTOs\UpdateProfileData;
use Modules\Settings\Events\ProfileUpdated;

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

test('update profile action dispatches an auditable updated event', function (): void {
    Event::fake([ProfileUpdated::class]);

    $user = User::factory()->create();

    UpdateProfile::handle($user, new UpdateProfileData(
        name: 'Updated User',
        email: 'updated@example.com',
    ));

    Event::assertDispatched(ProfileUpdated::class, fn (ProfileUpdated $event): bool => $event->auditEvent() === 'settings.profile_updated'
        && $event->auditSubjectId() === $user->id);
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
