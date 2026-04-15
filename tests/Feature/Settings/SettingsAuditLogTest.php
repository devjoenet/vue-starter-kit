<?php

declare(strict_types=1);

use Modules\Audit\Models\AuditLog;
use Modules\Core\Models\User;

test('password updates create self-service audit logs', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('user-password.edit'))
        ->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertRedirect(route('user-password.edit'));

    $auditLog = AuditLog::query()->latest('id')->first();

    expect($auditLog)->not->toBeNull();
    expect($auditLog?->event)->toBe('settings.password_updated');
    expect($auditLog?->actor_type)->toBe(User::class);
    expect($auditLog?->actor_id)->toBe($user->id);
    expect($auditLog?->actor_label)->toBe($user->email);
    expect($auditLog?->subject_type)->toBe(User::class);
    expect($auditLog?->subject_id)->toBe($user->id);
    expect($auditLog?->context)->toBe(['password_updated' => true]);
});
