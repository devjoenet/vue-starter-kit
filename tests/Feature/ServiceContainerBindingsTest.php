<?php

declare(strict_types=1);

use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;
use App\Modules\IAM\Actions\DashboardMetrics;
use App\Modules\IAM\Actions\GroupedPermissions;
use App\Modules\IAM\Actions\PermissionFilterOptionsCatalog;
use App\Modules\IAM\Actions\PermissionGroupCatalog;
use App\Modules\IAM\Actions\RoleFilterOptionsCatalog;
use App\Modules\IAM\Actions\UserFilterOptionsCatalog;
use App\Modules\IAM\Contracts\GroupedPermissionsProvider;
use App\Modules\IAM\Contracts\PermissionFilterOptionsProvider;
use App\Modules\IAM\Contracts\PermissionGroupCatalogContract;
use App\Modules\IAM\Contracts\RoleFilterOptionsProvider;
use App\Modules\IAM\Contracts\UserFilterOptionsProvider;

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
