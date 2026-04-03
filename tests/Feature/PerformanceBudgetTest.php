<?php

declare(strict_types=1);

use App\Modules\Users\Models\User;
use Database\Seeders\AdminAclSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    // Arrange

    $this->seed(AdminAclSeeder::class);

    $admin = User::factory()->create([
        'email' => 'performance-budget-admin@example.com',
        'email_verified_at' => now(),
    ]);
    $admin->assignRole('super-admin');

    $this->actingAs($admin);
});

test('admin read routes stay inside the current query-count budgets', function (): void {
    // Arrange

    $routes = [
        'admin.dashboard' => config('performance.budgets.query_count.admin_dashboard'),
        'admin.users.index' => config('performance.budgets.query_count.admin_users_index'),
        'admin.roles.index' => config('performance.budgets.query_count.admin_roles_index'),
        'admin.permissions.index' => config('performance.budgets.query_count.admin_permissions_index'),
    ];

    // Act

    foreach ($routes as $routeName => $budget) {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->get(route($routeName))->assertOk();

        $queryCount = count(DB::getQueryLog());

        // Assert

        expect($queryCount)->toBeLessThanOrEqual($budget);
    }

    DB::disableQueryLog();
});

test('high-identity first-load responses stay inside the current byte budgets', function (): void {
    // Act

    $welcomeResponse = $this->get('/');
    $dashboardResponse = $this->get(route('admin.dashboard'));
    $usersIndexResponse = $this->get(route('admin.users.index'));

    // Assert

    $welcomeResponse->assertOk();
    $dashboardResponse->assertOk();
    $usersIndexResponse->assertOk();

    expect(mb_strlen((string) $welcomeResponse->getContent()))
        ->toBeLessThanOrEqual(config('performance.budgets.response_bytes.welcome_page'));
    expect(mb_strlen((string) $dashboardResponse->getContent()))
        ->toBeLessThanOrEqual(config('performance.budgets.response_bytes.admin_dashboard'));
    expect(mb_strlen((string) $usersIndexResponse->getContent()))
        ->toBeLessThanOrEqual(config('performance.budgets.response_bytes.admin_users_index'));
});
