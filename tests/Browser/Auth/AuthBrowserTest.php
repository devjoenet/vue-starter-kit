<?php

declare(strict_types=1);

use App\Modules\Users\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;

test('public auth pages render without javascript errors', function () {
    // Arrange

    $pages = [
        route('login', absolute: false),
        route('password.request', absolute: false),
    ];

    if (Features::enabled(Features::registration())) {
        $pages[] = route('register', absolute: false);
    }

    // Act

    $browserPages = visit($pages);

    // Assert

    $browserPages->assertNoJavaScriptErrors();
});

test('users can sign in and browse key workspace pages without javascript errors', function () {
    // Arrange

    $user = User::factory()->create();

    // Act

    $page = visit(route('login', absolute: false));

    $page->assertNoJavaScriptErrors()
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('#auth-login-submit-button')
        ->assertPathIs(route('admin.dashboard', absolute: false));

    $workspacePages = visit([
        route('admin.dashboard', absolute: false),
        route('profile.edit', absolute: false),
        route('user-password.edit', absolute: false),
    ]);

    // Assert

    $page->assertNoJavaScriptErrors();
    $workspacePages->assertNoJavaScriptErrors();
    $this->assertAuthenticated();
    expect(auth()->id())->toBe($user->id);
});

test('login page keeps its primary controls keyboard reachable', function () {
    // Arrange

    $page = visit(route('login', absolute: false));

    $activeElementId = static fn ($browserPage): string => (string) $browserPage->script(
        "document.activeElement?.id ?? ''",
    );

    // Act

    $page->click('#email');

    expect($activeElementId($page))->toBe('email');

    $page->click('#password');
    $page->keys('#password', 'Tab');

    // Assert

    expect($activeElementId($page))->toBe('remember');

    $page->keys('#remember', 'Tab');

    expect($activeElementId($page))->toBe('auth-login-submit-button');

    $page->assertNoJavaScriptErrors();
});

test('users can register through the browser when registration is enabled', function () {
    if (! Features::enabled(Features::registration())) {
        $this->markTestSkipped('Registration is not enabled.');
    }

    // Arrange

    $email = 'browser-register@example.com';

    // Act

    $page = visit(route('register', absolute: false));

    $page->assertNoJavaScriptErrors()
        ->fill('name', 'Browser Register User')
        ->fill('email', $email)
        ->fill('password', 'password')
        ->fill('password_confirmation', 'password')
        ->click('#auth-register-submit-button');

    // Assert

    $page->assertPathIs(route('verification.notice', absolute: false))
        ->assertSee('Send another verification email')
        ->assertNoJavaScriptErrors();

    $this->assertAuthenticated();
    expect(User::query()->where('email', $email)->exists())->toBeTrue();
});

test('users can request a reset link and reset their password through the browser', function () {
    if (! Features::enabled(Features::resetPasswords())) {
        $this->markTestSkipped('Password resets are not enabled.');
    }

    // Arrange

    Notification::fake();

    $user = User::factory()->create([
        'email' => 'browser-reset@example.com',
    ]);

    // Act

    $requestPage = visit(route('password.request', absolute: false));

    $requestPage->assertNoJavaScriptErrors()
        ->fill('email', $user->email)
        ->click('#auth-forgot-password-submit-button')
        ->assertSee('Check your inbox')
        ->assertNoJavaScriptErrors();

    $resetUrl = null;

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user, &$resetUrl): bool {
        $actionUrl = (string) $notification->toMail($user)->actionUrl;
        $path = (string) parse_url($actionUrl, PHP_URL_PATH);
        $query = parse_url($actionUrl, PHP_URL_QUERY);

        $resetUrl = $query ? "{$path}?{$query}" : $path;

        return true;
    });

    expect($resetUrl)->not->toBeNull();

    $resetPage = $requestPage->navigate((string) $resetUrl);

    $resetPage->assertNoJavaScriptErrors()
        ->fill('password', 'new-password')
        ->fill('password_confirmation', 'new-password');

    $resetPage->script("document.querySelector('#auth-reset-password-form')?.requestSubmit();");

    $loginPage = $resetPage->assertPathIs(route('login', absolute: false))
        ->assertNoJavaScriptErrors();

    $loginPage->fill('email', $user->email)
        ->fill('password', 'new-password')
        ->click('#auth-login-submit-button');

    // Assert

    $loginPage->assertPathIs(route('admin.dashboard', absolute: false))
        ->assertNoJavaScriptErrors();

    $this->assertAuthenticated();
    expect(auth()->id())->toBe($user->id);
});

test('unverified users can resend and complete email verification through the browser', function () {
    if (! Features::enabled(Features::emailVerification())) {
        $this->markTestSkipped('Email verification is not enabled.');
    }

    // Arrange

    Notification::fake();

    $user = User::factory()->unverified()->create([
        'email' => 'browser-verify@example.com',
    ]);

    // Act

    $page = visit(route('login', absolute: false));

    $page->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('#auth-login-submit-button')
        ->assertPathIs(route('verification.notice', absolute: false))
        ->assertNoJavaScriptErrors()
        ->click('#auth-verify-email-submit-button')
        ->assertSee('Verification email sent')
        ->assertNoJavaScriptErrors();

    Notification::assertSentTo($user, VerifyEmail::class);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $path = (string) parse_url($verificationUrl, PHP_URL_PATH);
    $query = parse_url($verificationUrl, PHP_URL_QUERY);
    $relativeVerificationUrl = $query ? "{$path}?{$query}" : $path;

    $page->navigate($relativeVerificationUrl);

    // Assert

    $page->assertPathIs(route('admin.dashboard', absolute: false))
        ->assertNoJavaScriptErrors();

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('users can confirm their password before accessing protected settings', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    // Arrange

    $user = User::factory()->create();

    // Act

    $page = visit(route('login', absolute: false));

    $page->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('#auth-login-submit-button')
        ->navigate(route('two-factor.show', absolute: false))
        ->assertPathIs(route('password.confirm', absolute: false))
        ->assertNoJavaScriptErrors()
        ->fill('password', 'password')
        ->click('#auth-confirm-password-submit-button');

    // Assert

    $page->assertPathIs(route('two-factor.show', absolute: false))
        ->assertSee('Two-factor authentication')
        ->assertNoJavaScriptErrors();
});
