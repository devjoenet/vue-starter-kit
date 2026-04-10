<?php

declare(strict_types=1);

use App\Modules\IAM\Models\Permission;
use App\Modules\IAM\Models\PermissionGroup;
use App\Modules\Shared\Models\User;
use Spatie\Permission\PermissionRegistrar;

test('dashboard, admin, and settings pages stay inside the sidebar workspace shell', function () {
    // Arrange

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $permissionGroups = [
        'users' => PermissionGroup::query()->firstOrCreate(['slug' => 'users'], ['label' => 'Users']),
        'roles' => PermissionGroup::query()->firstOrCreate(['slug' => 'roles'], ['label' => 'Roles']),
        'permissions' => PermissionGroup::query()->firstOrCreate(['slug' => 'permissions'], ['label' => 'Permissions']),
    ];

    foreach ([
        'users.view' => 'users',
        'roles.view' => 'roles',
        'permissions.view' => 'permissions',
    ] as $permissionName => $groupSlug) {
        Permission::query()->firstOrCreate(
            ['name' => $permissionName, 'guard_name' => 'web'],
            ['permission_group_id' => $permissionGroups[$groupSlug]->id],
        );
    }

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $user = User::factory()->create([
        'email' => 'browser-sidebar-shell@example.com',
    ]);
    $user->givePermissionTo(['users.view', 'roles.view', 'permissions.view']);

    $this->actingAs($user);

    // Act

    $dashboardPage = visit(route('admin.dashboard', absolute: false));

    // Assert

    $dashboardPage
        ->assertPresent('#admin-dashboard-page')
        ->assertSee('Workspace')
        ->assertSee('Access control, settings, and starter surfaces shaped for client-ready demos.')
        ->assertNoJavaScriptErrors();

    $adminPage = $dashboardPage->navigate(route('admin.users.index', absolute: false));

    $adminPage
        ->assertPresent('#admin-users-index-page')
        ->assertSee('Workspace')
        ->assertSee('Access control, settings, and starter surfaces shaped for client-ready demos.')
        ->assertNoJavaScriptErrors();

    $settingsPage = $adminPage->navigate(route('user-password.edit', absolute: false));

    $settingsPage
        ->assertPresent('#settings-password-page')
        ->assertPresent('#settings-layout-nav')
        ->assertSee('Workspace')
        ->assertSee('Access control, settings, and starter surfaces shaped for client-ready demos.')
        ->assertNoJavaScriptErrors();
});

test('admin and settings forms keep a predictable keyboard order', function () {
    // Arrange

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $permissionGroups = [
        'users' => PermissionGroup::query()->firstOrCreate(['slug' => 'users'], ['label' => 'Users']),
        'roles' => PermissionGroup::query()->firstOrCreate(['slug' => 'roles'], ['label' => 'Roles']),
        'permissions' => PermissionGroup::query()->firstOrCreate(['slug' => 'permissions'], ['label' => 'Permissions']),
    ];

    foreach ([
        'users.view' => 'users',
        'users.create' => 'users',
        'roles.view' => 'roles',
        'permissions.view' => 'permissions',
    ] as $permissionName => $groupSlug) {
        Permission::query()->firstOrCreate(
            ['name' => $permissionName, 'guard_name' => 'web'],
            ['permission_group_id' => $permissionGroups[$groupSlug]->id],
        );
    }

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $user = User::factory()->create([
        'email' => 'browser-workspace-focus@example.com',
    ]);
    $user->givePermissionTo(['users.view', 'users.create', 'roles.view', 'permissions.view']);

    $this->actingAs($user);

    $activeElementId = static fn ($browserPage): string => (string) $browserPage->script(
        "document.activeElement?.id ?? ''",
    );
    // Act

    $adminPage = visit(route('admin.users.create', absolute: false));
    $adminPage->click('#create-user-name');

    // Assert

    expect($activeElementId($adminPage))->toBe('create-user-name');

    $adminPage->keys('#create-user-name', 'Tab');

    expect($activeElementId($adminPage))->toBe('create-user-email');

    $adminPage->keys('#create-user-email', 'Tab');

    expect($activeElementId($adminPage))->toBe('create-user-password');

    $adminPage->keys('#create-user-password', 'Tab');

    expect($activeElementId($adminPage))->toBe('create-user-password-confirmation');

    $adminPage->assertNoJavaScriptErrors();

    $settingsPage = $adminPage->navigate(route('user-password.edit', absolute: false));
    $settingsPage->click('#current_password');

    expect($activeElementId($settingsPage))->toBe('current_password');

    $settingsPage->keys('#current_password', 'Tab');

    expect($activeElementId($settingsPage))->toBe('password');

    $settingsPage->keys('#password', 'Tab');

    expect($activeElementId($settingsPage))->toBe('password_confirmation');

    $settingsPage->keys('#password_confirmation', 'Tab');

    expect($activeElementId($settingsPage))->toBe('settings-password-save-button');

    $settingsPage->assertNoJavaScriptErrors();
});

test('profile delete dialog keeps focus on destructive controls and returns attention to the trigger', function () {
    // Arrange

    $user = User::factory()->create([
        'email' => 'browser-profile-delete@example.com',
    ]);

    $this->actingAs($user);

    $waitForActiveElementAttribute = static fn ($browserPage, string $attribute, string $expectedValue): string => (string) $browserPage->script(<<<JS
        (async () => {
            for (let attempt = 0; attempt < 30; attempt += 1) {
                if (document.activeElement?.getAttribute('{$attribute}') === '{$expectedValue}') {
                    return document.activeElement.getAttribute('{$attribute}');
                }

                await new Promise((resolve) => setTimeout(resolve, 100));
            }

            return document.activeElement?.getAttribute('{$attribute}') ?? '';
        })()
    JS);
    $focusLivesInsideDialog = static fn ($browserPage): bool => (bool) $browserPage->script(
        "document.activeElement?.closest('[data-slot=\"dialog-content\"]') !== null",
    );

    // Act

    $page = visit(route('profile.edit', absolute: false));

    $page->click('[data-test="delete-user-button"]');

    // Assert

    $page->assertVisible('[data-slot="dialog-content"]');

    expect($focusLivesInsideDialog($page))->toBeTrue();

    $page->script("document.querySelector('#settings-profile-delete-account-form')?.requestSubmit();");

    expect($waitForActiveElementAttribute($page, 'name', 'password'))->toBe('password');
    expect($focusLivesInsideDialog($page))->toBeTrue();

    $page->script(<<<'JS'
        Array.from(document.querySelectorAll('[data-slot="dialog-content"] button'))
            .find((button) => button.textContent?.trim() === 'Cancel')
            ?.click();
    JS);

    expect($waitForActiveElementAttribute($page, 'data-test', 'delete-user-button'))->toBe('delete-user-button');

    $page->assertNoJavaScriptErrors();
});
