<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;
use Modules\Core\Models\User;

test('login page supports partial reloads for availability and status props', function () {
    // Arrange

    $status = 'Password reset completed.';

    // Act

    $response = $this->withSession(['status' => $status])->get(route('login'));

    // Assert

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Auth/Login')
            ->where('status', $status)
            ->reloadOnly(['status'], fn (Assert $reload) => $reload
                ->where('status', $status)
                ->missing('canResetPassword')
                ->missing('canRegister')
            )
            ->reloadOnly(['canResetPassword', 'canRegister'], fn (Assert $reload) => $reload
                ->has('canResetPassword')
                ->has('canRegister')
                ->missing('status')
            )
        );
});

test('forgot password page supports partial reloads for status messaging', function () {
    // Arrange

    $status = 'We have emailed your password reset link!';

    // Act

    $response = $this->withSession(['status' => $status])->get(route('password.request'));

    // Assert

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Auth/ForgotPassword')
            ->where('status', $status)
            ->reloadOnly(['status'], fn (Assert $reload) => $reload
                ->where('status', $status)
            )
        );
});

test('email verification page supports partial reloads for status messaging', function () {
    // Arrange

    $user = User::factory()->unverified()->create();

    // Act

    $response = $this->actingAs($user)
        ->withSession(['status' => 'verification-link-sent'])
        ->get(route('verification.notice'));

    // Assert

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Auth/VerifyEmail')
            ->where('status', 'verification-link-sent')
            ->reloadOnly(['status'], fn (Assert $reload) => $reload
                ->where('status', 'verification-link-sent')
                ->missing('auth')
            )
        );
});
