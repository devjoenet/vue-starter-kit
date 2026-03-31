<?php

declare(strict_types=1);

use App\Modules\Dashboard\Actions\DashboardMetrics;
use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;
use App\Modules\Permissions\Actions\PermissionFilterOptionsCatalog;
use App\Modules\Permissions\Actions\PermissionGroupCatalog;
use App\Modules\Permissions\Contracts\PermissionFilterOptionsProvider;
use App\Modules\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\Roles\Actions\GroupedPermissions;
use App\Modules\Roles\Actions\RoleFilterOptionsCatalog;
use App\Modules\Roles\Contracts\GroupedPermissionsProvider;
use App\Modules\Roles\Contracts\RoleFilterOptionsProvider;
use App\Modules\Users\Actions\UserFilterOptionsCatalog;
use App\Modules\Users\Contracts\UserFilterOptionsProvider;

dataset('bound_module_contracts', [
    [DashboardMetricsProvider::class, DashboardMetrics::class],
    [PermissionGroupCatalogContract::class, PermissionGroupCatalog::class],
    [PermissionFilterOptionsProvider::class, PermissionFilterOptionsCatalog::class],
    [GroupedPermissionsProvider::class, GroupedPermissions::class],
    [RoleFilterOptionsProvider::class, RoleFilterOptionsCatalog::class],
    [UserFilterOptionsProvider::class, UserFilterOptionsCatalog::class],
]);

test('reusable admin contracts resolve to the expected concrete collaborators', function (
    string $contractClass,
    string $implementationClass,
): void {
    expect(app($contractClass))->toBeInstanceOf($implementationClass);
})->with('bound_module_contracts');
