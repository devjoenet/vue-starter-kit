<?php

declare(strict_types=1);

use App\Modules\Audit\Models\AuditLog;
use App\Modules\IAM\Permissions\Models\Permission;
use App\Modules\IAM\Roles\Models\Role;
use App\Modules\Shared\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\AdminAclSeeder;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->seed(AdminAclSeeder::class);
});

test('audit log index renders paginated filtered audit data', function (): void {
    // Arrange

    $admin = User::factory()->create();
    $admin->assignRole('super-admin');
    $this->actingAs($admin);

    $oldLog = AuditLog::query()->create([
        'actor_type' => User::class,
        'actor_id' => $admin->id,
        'actor_label' => 'avery@example.test',
        'event' => 'users.deleted',
        'subject_type' => User::class,
        'subject_id' => 101,
        'subject_label' => 'Former User',
        'summary' => 'Removed former user access.',
        'method' => 'DELETE',
        'url' => 'https://example.test/admin/users/101',
    ]);
    $oldLog->forceFill([
        'created_at' => CarbonImmutable::parse('2026-04-07 09:00:00'),
    ])->save();

    $matchingLog = AuditLog::query()->create([
        'actor_type' => User::class,
        'actor_id' => $admin->id,
        'actor_label' => 'casey@example.test',
        'event' => 'roles.permissions_synced',
        'subject_type' => Role::class,
        'subject_id' => 202,
        'subject_label' => 'Ops Role',
        'summary' => 'Synced operator-facing permissions.',
        'method' => 'PUT',
        'url' => 'https://example.test/admin/roles/202/permissions',
    ]);
    $matchingLog->forceFill([
        'created_at' => CarbonImmutable::parse('2026-04-08 14:30:00'),
    ])->save();

    $otherLog = AuditLog::query()->create([
        'actor_type' => User::class,
        'actor_id' => $admin->id,
        'actor_label' => 'blake@example.test',
        'event' => 'permissions.updated',
        'subject_type' => Permission::class,
        'subject_id' => 303,
        'subject_label' => 'Billing Export',
        'summary' => 'Updated billing permission catalog metadata.',
        'method' => 'PUT',
        'url' => 'https://example.test/admin/permissions/303',
    ]);
    $otherLog->forceFill([
        'created_at' => CarbonImmutable::parse('2026-04-08 16:45:00'),
    ])->save();

    // Act

    $response = get(route('admin.audit-logs.index', [
        'sort' => 'event',
        'direction' => 'asc',
        'actors' => ['casey@example.test'],
        'events' => ['roles.permissions_synced', 'users.deleted'],
        'subject_types' => [Role::class],
        'search' => 'operator',
        'from' => '2026-04-08',
        'until' => '2026-04-08',
    ]));

    // Assert

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/AuditLogs/Index')
            ->where('query.sort', 'event')
            ->where('query.direction', 'asc')
            ->where('query.actors', ['casey@example.test'])
            ->where('query.events', ['roles.permissions_synced', 'users.deleted'])
            ->where('query.subject_types', [Role::class])
            ->where('query.search', 'operator')
            ->where('query.from', '2026-04-08')
            ->where('query.until', '2026-04-08')
            ->where('auditLogs.data.0.id', $matchingLog->id)
            ->where('auditLogs.data.0.event', 'roles.permissions_synced')
            ->where('auditLogs.data.0.actor_label', 'casey@example.test')
            ->where('auditLogs.data.0.subject_type', Role::class)
            ->has('auditLogs.data', 1)
            ->where('filterOptions.actors', [
                'avery@example.test',
                'blake@example.test',
                'casey@example.test',
            ])
            ->where('filterOptions.events', [
                'permissions.updated',
                'roles.permissions_synced',
                'users.deleted',
            ])
            ->where('filterOptions.subject_types', [
                Permission::class,
                Role::class,
                User::class,
            ])
            ->reloadOnly(['auditLogs', 'filterOptions', 'query'], fn (Assert $reload) => $reload
                ->has('auditLogs.data')
                ->has('filterOptions.actors')
                ->has('query')
            )
        );
});

test('audit log index forbids users without the audit permission', function (): void {
    // Arrange

    $user = User::factory()->create();

    $this->actingAs($user);

    // Act

    $response = get(route('admin.audit-logs.index'));

    // Assert

    $response
        ->assertForbidden();
});
