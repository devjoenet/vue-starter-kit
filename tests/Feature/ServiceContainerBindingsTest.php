<?php

declare(strict_types=1);

use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;
use App\Modules\IAM\Auth\Actions\GetAuthCounts;
use App\Modules\IAM\Permissions\Actions\PermissionFilterOptionsCatalog;
use App\Modules\IAM\Permissions\Actions\PermissionGroupCatalog;
use App\Modules\IAM\Permissions\Contracts\PermissionFilterOptionsProvider;
use App\Modules\IAM\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\IAM\Roles\Actions\GroupedPermissions;
use App\Modules\IAM\Roles\Actions\RoleFilterOptionsCatalog;
use App\Modules\IAM\Roles\Contracts\GroupedPermissionsProvider;
use App\Modules\IAM\Roles\Contracts\RoleFilterOptionsProvider;
use App\Modules\IAM\Users\Actions\UserFilterOptionsCatalog;
use App\Modules\IAM\Users\Contracts\UserFilterOptionsProvider;

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
