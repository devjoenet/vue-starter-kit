<?php

declare(strict_types=1);

namespace Modules\Auth\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class AuthServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Auth';

    protected string $nameLower = 'auth';

    protected array $providers = [
        EventServiceProvider::class,
        FortifyServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
