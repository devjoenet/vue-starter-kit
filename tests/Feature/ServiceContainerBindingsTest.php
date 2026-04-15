<?php

declare(strict_types=1);

use Modules\Auth\Actions\GetAuthCounts;
use Modules\Dashboard\Contracts\DashboardMetricsProvider;
use Modules\Permissions\Actions\PermissionFilterOptionsCatalog;
use Modules\Permissions\Actions\PermissionGroupCatalog;
use Modules\Permissions\Contracts\PermissionFilterOptionsProvider;
use Modules\Permissions\Contracts\PermissionGroupCatalogContract;
use Modules\Roles\Actions\GroupedPermissions;
use Modules\Roles\Actions\RoleFilterOptionsCatalog;
use Modules\Roles\Contracts\GroupedPermissionsProvider;
use Modules\Roles\Contracts\RoleFilterOptionsProvider;
use Modules\Users\Actions\UserFilterOptionsCatalog;
use Modules\Users\Contracts\UserFilterOptionsProvider;

dataset('bound_module_contracts', [
    [DashboardMetricsProvider::class, GetAuthCounts::class],
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
