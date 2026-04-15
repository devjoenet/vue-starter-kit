<?php

declare(strict_types=1);

namespace Modules\Permissions\Providers;

use Modules\Permissions\Actions\PermissionFilterOptionsCatalog;
use Modules\Permissions\Actions\PermissionGroupCatalog;
use Modules\Permissions\Console\CreatePermissionCommand;
use Modules\Permissions\Contracts\PermissionFilterOptionsProvider;
use Modules\Permissions\Contracts\PermissionGroupCatalogContract;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Override;

class PermissionsServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Permissions';

    protected string $nameLower = 'permissions';

    protected array $commands = [
        CreatePermissionCommand::class,
    ];

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    #[Override]
    public function register(): void
    {
        parent::register();

        $this->app->singleton(PermissionGroupCatalogContract::class, PermissionGroupCatalog::class);
        $this->app->singleton(PermissionFilterOptionsProvider::class, PermissionFilterOptionsCatalog::class);
    }
}
