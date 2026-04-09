<?php

declare(strict_types=1);

use App\Modules\Shared\Models\User;
use Laravel\Fortify\Features;
use PragmaRX\Google2FA\Google2FA;

test('users can enable, confirm, use recovery codes for, and disable two factor authentication through the browser', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    // Arrange

    $user = User::factory()->create([
        'email' => 'browser-two-factor@example.com',
    ]);

    /** @var Google2FA $google2fa */
    $google2fa = app(Google2FA::class);

    // Act

    $page = visit(route('login', absolute: false));

    $page->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('#auth-login-submit-button')
        ->navigate(route('two-factor.show', absolute: false));

    $page->assertPathIs(route('password.confirm', absolute: false))
        ->assertNoJavaScriptErrors()
        ->fill('password', 'password');

    $page->script("document.querySelector('#auth-confirm-password-form')?.requestSubmit();");

    $page->assertPathIs(route('two-factor.show', absolute: false))
        ->assertNoJavaScriptErrors()
        ->click('#settings-two-factor-enable-button')
        ->assertSee('Enable Two-Factor Authentication')
        ->assertNoJavaScriptErrors();

    $secretKeyResponse = (string) $page->script(<<<'JS'
        (async () => {
            for (let attempt = 0; attempt < 10; attempt++) {
                const response = await fetch('/user/two-factor-secret-key', {
                    headers: { Accept: 'application/json' },
                });

                if (response.ok) {
                    const body = await response.text();

                    if (body) {
                        return body;
                    }
                }

                await new Promise((resolve) => setTimeout(resolve, 200));
            }

            return '';
        })()
    JS);

    $manualSetupKey = json_decode($secretKeyResponse, true)['secretKey'] ?? '';
    expect($manualSetupKey)->not->toBe('');

    $otp = $google2fa->getCurrentOtp($manualSetupKey);

    $page->click('Continue')
        ->assertSee('Verify Authentication Code')
        ->type('[data-input-otp]', $otp)
        ->click('Confirm')
        ->assertSee('Enabled')
        ->assertNoJavaScriptErrors();

    $page->click('Reveal Recovery Codes')
        ->assertNoJavaScriptErrors();

    $recoveryCodesResponse = (string) $page->script(<<<'JS'
        (async () => {
            for (let attempt = 0; attempt < 10; attempt++) {
                const response = await fetch('/user/two-factor-recovery-codes', {
                    headers: { Accept: 'application/json' },
                });

                if (response.ok) {
                    return await response.text();
                }

                await new Promise((resolve) => setTimeout(resolve, 200));
            }

            return '[]';
        })()
    JS);

    $recoveryCodes = json_decode($recoveryCodesResponse, true) ?? [];
    $recoveryCode = $recoveryCodes[0] ?? null;

    expect($recoveryCode)->not->toBeNull();

    $page->assertSee($recoveryCode);

    $this->post(route('logout'));

    $page->navigate(route('login', absolute: false))
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('#auth-login-submit-button')
        ->assertPathIs(route('two-factor.login', absolute: false))
        ->assertNoJavaScriptErrors()
        ->click('#auth-two-factor-switch-to-recovery-button')
        ->fill('recovery_code', $recoveryCode)
        ->click('#auth-two-factor-recovery-submit-button')
        ->assertPathIs(route('admin.dashboard', absolute: false))
        ->assertNoJavaScriptErrors()
        ->navigate(route('two-factor.show', absolute: false))
        ->assertPathIs(route('two-factor.show', absolute: false))
        ->click('#settings-two-factor-disable-button');

    // Assert

    $page->assertSee('Disabled')
        ->assertPresent('#settings-two-factor-enable-button')
        ->assertNoJavaScriptErrors();

    expect($user->fresh()->hasEnabledTwoFactorAuthentication())->toBeFalse();
});
