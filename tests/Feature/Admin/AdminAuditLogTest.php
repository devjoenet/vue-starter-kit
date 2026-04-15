<?php

declare(strict_types=1);

use Database\Seeders\AdminAclSeeder;
use Modules\Audit\Actions\GetAuditHistoryItems;
use Modules\Audit\DTOs\AuditHistoryChangeData;
use Modules\Audit\Models\AuditLog;
use Modules\Core\Models\User;
use Modules\Permissions\Models\Permission;
use Modules\Permissions\Models\PermissionGroup;
use Modules\Roles\Models\Role;

use function Pest\Laravel\delete;
use function Pest\Laravel\put;

beforeEach(function (): void {
    $this->seed(AdminAclSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('super-admin');

    $this->actingAs($admin);
});

test('admin user deletions create durable audit logs', function (): void {
    $target = User::factory()->create([
        'email' => 'delete-me@example.test',
    ]);

    delete(route('admin.users.destroy', $target))
        ->assertRedirect(route('admin.users.index'));

    $auditLog = AuditLog::query()->latest('id')->first();
    assert($auditLog instanceof AuditLog);
    /** @var array<string, mixed> $changes */
    $changes = $auditLog->changes ?? [];

    expect($auditLog->event)->toBe('users.deleted');
    expect($auditLog->actor_type)->toBe(User::class);
    expect($auditLog->actor_id)->toBe(auth()->id());
    expect($auditLog->actor_label)->toBe(auth()->user()?->email);
    expect($auditLog->subject_type)->toBe(User::class);
    expect($auditLog->subject_id)->toBe($target->id);
    expect($auditLog->subject_label)->toBe('delete-me@example.test');
    expect($changes['before'])->toMatchArray([
        'email' => 'delete-me@example.test',
    ]);
    expect($changes['after'])->toBeNull();
});

test('admin role permission sync writes an audit trail', function (): void {
    $role = Role::query()->create([
        'name' => 'editor',
        'guard_name' => 'web',
    ]);
    $usersGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'users'],
        ['label' => 'User Administration'],
    );
    $permission = Permission::query()->create([
        'name' => 'users.manageMembers',
        'label' => 'Manage Members',
        'permission_group_id' => $usersGroup->id,
        'guard_name' => 'web',
    ]);

    put(route('admin.roles.permissions.sync', $role), [
        'permissions' => [$permission->name],
    ])->assertRedirect();

    $auditLog = AuditLog::query()->latest('id')->first();
    assert($auditLog instanceof AuditLog);
    /** @var array<string, mixed> $changes */
    $changes = $auditLog->changes ?? [];

    expect($auditLog->event)->toBe('roles.permissions_synced');
    expect($auditLog->actor_label)->toBe(auth()->user()?->email);
    expect($auditLog->subject_type)->toBe(Role::class);
    expect($auditLog->subject_id)->toBe($role->id);
    expect($changes)->toBe([
        'before' => ['permissions' => []],
        'after' => ['permissions' => [$permission->name]],
    ]);
});

test('admin permission updates capture before and after catalog state', function (): void {
    $originalGroup = PermissionGroup::query()->firstOrCreate([
        'slug' => 'users',
    ], [
        'label' => 'User Administration',
        'description' => 'Original group copy.',
    ]);
    $permission = Permission::query()->create([
        'name' => 'users.viewReports',
        'label' => 'View Reports',
        'description' => 'Original permission copy.',
        'permission_group_id' => $originalGroup->id,
        'guard_name' => 'web',
    ]);

    put(route('admin.permissions.update', $permission), [
        'name' => $permission->name,
        'label' => 'Manage Reports',
        'description' => 'Updated permission copy.',
        'group' => 'roles',
        'group_label' => 'Role Management',
        'group_description' => 'Updated group copy.',
    ])->assertRedirect();

    $auditLog = AuditLog::query()->latest('id')->first();
    assert($auditLog instanceof AuditLog);
    /** @var array<string, mixed> $changes */
    $changes = $auditLog->changes ?? [];

    expect($auditLog->event)->toBe('permissions.updated');
    expect($auditLog->actor_label)->toBe(auth()->user()?->email);
    expect($auditLog->subject_type)->toBe(Permission::class);
    expect($auditLog->subject_id)->toBe($permission->id);
    expect($changes['before'])->toMatchArray([
        'label' => 'View Reports',
        'description' => 'Original permission copy.',
        'group' => 'users',
        'group_label' => 'User Administration',
    ]);
    expect($changes['after'])->toMatchArray([
        'label' => 'Manage Reports',
        'description' => 'Updated permission copy.',
        'group' => 'roles',
        'group_label' => 'Role Management',
    ]);
});

test('record audit history redacts sensitive change fields', function (): void {
    $target = User::factory()->create([
        'email' => 'audited-user@example.test',
    ]);
    AuditLog::query()->create([
        'event' => 'users.updated',
        'subject_type' => User::class,
        'subject_id' => $target->id,
        'subject_label' => $target->email,
        'summary' => 'Updated user with sensitive fields.',
        'changes' => [
            'before' => [
                'email' => 'old-user@example.test',
                'password' => 'old-password',
            ],
            'after' => [
                'email' => $target->email,
                'password' => 'new-password',
            ],
        ],
    ]);

    $auditHistory = GetAuditHistoryItems::handle($target);
    $auditHistoryItem = $auditHistory->first();
    assert($auditHistoryItem !== null);
    $changes = $auditHistoryItem->changes->keyBy('field');
    $emailChange = $changes->get('email');
    $passwordChange = $changes->get('password');
    assert($emailChange instanceof AuditHistoryChangeData);
    assert($passwordChange instanceof AuditHistoryChangeData);

    expect($emailChange->after)->toBe('audited-user@example.test');
    expect($passwordChange->before)->toBe('Redacted');
    expect($passwordChange->after)->toBe('Redacted');
});
