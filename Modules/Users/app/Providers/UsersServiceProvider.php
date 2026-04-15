<?php

declare(strict_types=1);

namespace Modules\Users\Providers;

use Modules\Users\Actions\UserFilterOptionsCatalog;
use Modules\Users\Console\CreateUserCommand;
use Modules\Users\Contracts\UserFilterOptionsProvider;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Override;

class UsersServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Users';

    protected string $nameLower = 'users';

    protected array $commands = [
        CreateUserCommand::class,
    ];

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    #[Override]
    public function register(): void
    {
        parent::register();

        $this->app->singleton(UserFilterOptionsProvider::class, UserFilterOptionsCatalog::class);
    }
}
