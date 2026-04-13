<?php

declare(strict_types=1);

use App\Modules\IAM\Permissions\Actions\PermissionNormalizer;

it('normalizes permission groups and names into group.camelCase format', function (): void {
    $normalizer = app(PermissionNormalizer::class);

    expect($normalizer->normalize('USERS', 'Invite Team Members'))->toBe([
        'group' => 'users',
        'group_label' => 'Users',
        'group_description' => null,
        'name' => 'users.inviteTeamMembers',
        'label' => 'Invite Team Members',
        'description' => null,
    ]);

    expect($normalizer->normalize('Billing Ops', 'Issue Refund'))->toBe([
        'group' => 'billing_ops',
        'group_label' => 'Billing Ops',
        'group_description' => null,
        'name' => 'billing_ops.issueRefund',
        'label' => 'Issue Refund',
        'description' => null,
    ]);

    expect($normalizer->normalize('ROLES', 'roles.manage users'))->toBe([
        'group' => 'roles',
        'group_label' => 'Roles',
        'group_description' => null,
        'name' => 'roles.manageUsers',
        'label' => 'Manage Users',
        'description' => null,
    ]);
});
