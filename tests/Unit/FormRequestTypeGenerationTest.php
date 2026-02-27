<?php

declare(strict_types=1);

it('exports settings form request payload types', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/types/generated.d.ts');

    expect($contents)
        ->toContain('type StoreUserRequest')
        ->toContain('type UpdateUserRequest')
        ->toContain('type SyncUserRolesRequest')
        ->toContain('type StoreRoleRequest')
        ->toContain('type UpdateRoleRequest')
        ->toContain('type SyncRolePermissionsRequest')
        ->toContain('type StorePermissionRequest')
        ->toContain('type UpdatePermissionRequest')
        ->toContain('type ProfileUpdateRequest')
        ->toContain('type ProfileDeleteRequest')
        ->toContain('type PasswordUpdateRequest')
        ->toContain('type TwoFactorAuthenticationRequest');
});
