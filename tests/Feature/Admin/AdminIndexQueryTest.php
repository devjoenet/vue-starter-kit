<?php

declare(strict_types=1);

use App\Modules\IAM\Models\Permission;
use App\Modules\IAM\Models\PermissionGroup;
use App\Modules\IAM\Models\Role;
use App\Modules\Shared\Models\User;
use Database\Seeders\AdminAclSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    $this->seed(AdminAclSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('super-admin');

    $this->actingAs($admin);
});

test('users index applies OR-within-column and AND-across-column filters', function (): void {
    $reviewerRole = Role::query()->create([
        'name' => 'release_reviewer',
        'guard_name' => 'web',
    ]);
    $editorRole = Role::query()->create([
        'name' => 'content_editor',
        'guard_name' => 'web',
    ]);

    $avery = User::factory()->create([
        'name' => 'Avery Stone',
        'email' => 'avery@example.test',
    ]);
    $blake = User::factory()->create([
        'name' => 'Blake Hart',
        'email' => 'blake@example.test',
    ]);
    $casey = User::factory()->create([
        'name' => 'Casey Rowe',
        'email' => 'casey@example.test',
    ]);

    $avery->assignRole($reviewerRole);
    $blake->assignRole($editorRole);
    $casey->assignRole($reviewerRole);

    $this->get(route('admin.users.index', [
        'sort' => 'email',
        'direction' => 'desc',
        'filters' => [
            'name' => ['Avery Stone', 'Blake Hart'],
            'roles' => ['release_reviewer'],
        ],
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Users/Index')
            ->where('query.sort', 'email')
            ->where('query.direction', 'desc')
            ->where('query.filters.name', ['Avery Stone', 'Blake Hart'])
            ->where('query.filters.roles', ['release_reviewer'])
            ->where('users.data', fn ($rows): bool => collect($rows)->pluck('name')->all() === ['Avery Stone'])
        );
});

test('users pagination preserves active sort and filter query state', function (): void {
    $pagerRole = Role::query()->create([
        'name' => 'pager_role',
        'guard_name' => 'web',
    ]);

    User::factory()
        ->count(17)
        ->sequence(fn ($sequence): array => [
            'name' => "Pager {$sequence->index}",
            'email' => sprintf('pager-%02d@example.test', $sequence->index),
        ])
        ->create()
        ->each(fn (User $user) => $user->assignRole($pagerRole));

    $this->get(route('admin.users.index', [
        'sort' => 'email',
        'direction' => 'desc',
        'filters' => [
            'roles' => ['pager_role'],
        ],
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Users/Index')
            ->where('users.next_page_url', function (?string $nextPageUrl): bool {
                if ($nextPageUrl === null) {
                    return false;
                }

                return str_contains($nextPageUrl, 'sort=email')
                    && str_contains($nextPageUrl, 'direction=desc')
                    && str_contains($nextPageUrl, 'filters%5Broles%5D%5B0%5D=pager_role');
            })
        );
});

test('roles index sorts filtered rows by a single active column', function (): void {
    $alpha = Role::query()->create([
        'name' => 'alpha_manager',
        'guard_name' => 'web',
    ]);
    $beta = Role::query()->create([
        'name' => 'beta_manager',
        'guard_name' => 'web',
    ]);
    $gamma = Role::query()->create([
        'name' => 'gamma_manager',
        'guard_name' => 'web',
    ]);

    User::factory()->create()->assignRole($alpha);
    User::factory()->count(3)->create()->each(fn (User $user) => $user->assignRole($beta));
    $gamma->users()->detach();

    $this->get(route('admin.roles.index', [
        'sort' => 'users',
        'direction' => 'desc',
        'filters' => [
            'slug' => ['alpha_manager', 'beta_manager', 'gamma_manager'],
            'users' => ['1', '3'],
        ],
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Roles/Index')
            ->where('query.sort', 'users')
            ->where('query.direction', 'desc')
            ->where('query.filters.users', ['1', '3'])
            ->where('roles', fn ($roles): bool => collect($roles)->pluck('name')->all() === [
                'beta_manager',
                'alpha_manager',
            ])
        );
});

test('permissions index uses a flat listing contract and exact-match filters', function (): void {
    $auditLogsGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'audit_logs'],
        ['label' => 'Audit Logs'],
    );
    $billingGroup = PermissionGroup::query()->firstOrCreate(
        ['slug' => 'billing'],
        ['label' => 'Billing'],
    );

    Permission::query()->create([
        'name' => 'audit_logs.view',
        'label' => 'View Audit Logs',
        'permission_group_id' => $auditLogsGroup->id,
        'guard_name' => 'web',
    ]);
    Permission::query()->create([
        'name' => 'audit_logs.update',
        'label' => 'Update Audit Logs',
        'permission_group_id' => $auditLogsGroup->id,
        'guard_name' => 'web',
    ]);
    Permission::query()->create([
        'name' => 'billing.export',
        'label' => 'Export Billing',
        'permission_group_id' => $billingGroup->id,
        'guard_name' => 'web',
    ]);

    $this->get(route('admin.permissions.index', [
        'sort' => 'permission_check',
        'direction' => 'asc',
        'filters' => [
            'group' => ['audit_logs'],
            'permission' => ['Update Audit Logs', 'View Audit Logs'],
        ],
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Permissions/Index')
            ->missing('permissionsByGroup')
            ->has('groups')
            ->where('query.sort', 'permission_check')
            ->where('query.direction', 'asc')
            ->where('query.filters.group', ['audit_logs'])
            ->where('query.filters.permission', ['Update Audit Logs', 'View Audit Logs'])
            ->where('permissions', fn ($permissions): bool => collect($permissions)->pluck('name')->all() === [
                'audit_logs.update',
                'audit_logs.view',
            ])
        );
});
