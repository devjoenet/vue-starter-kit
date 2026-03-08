<?php

declare(strict_types=1);

use App\Support\PermissionNormalizer;

it('normalizes permission groups and names into group.camelCase format', function (): void {
    $normalizer = app(PermissionNormalizer::class);

    expect($normalizer->normalize('USERS', 'Invite Team Members'))->toBe([
        'group' => 'users',
        'name' => 'users.inviteTeamMembers',
    ]);

    expect($normalizer->normalize('Billing Ops', 'Issue Refund'))->toBe([
        'group' => 'billing_ops',
        'name' => 'billing_ops.issueRefund',
    ]);

    expect($normalizer->normalize('ROLES', 'roles.manage users'))->toBe([
        'group' => 'roles',
        'name' => 'roles.manageUsers',
    ]);
});
