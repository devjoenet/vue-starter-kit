<?php

declare(strict_types=1);

namespace Modules\Roles\Providers;

use Modules\Roles\Actions\GroupedPermissions;
use Modules\Roles\Actions\RoleFilterOptionsCatalog;
use Modules\Roles\Console\CreateRoleCommand;
use Modules\Roles\Contracts\GroupedPermissionsProvider;
use Modules\Roles\Contracts\RoleFilterOptionsProvider;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Override;

class RolesServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Roles';

    protected string $nameLower = 'roles';

    protected array $commands = [
        CreateRoleCommand::class,
    ];

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    #[Override]
    public function register(): void
    {
        parent::register();

        $this->app->singleton(GroupedPermissionsProvider::class, GroupedPermissions::class);
        $this->app->singleton(RoleFilterOptionsProvider::class, RoleFilterOptionsCatalog::class);
    }
}
