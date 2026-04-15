<?php

declare(strict_types=1);

namespace Modules\Dashboard\Providers;

use Modules\Auth\Actions\GetAuthCounts;
use Modules\Dashboard\Contracts\DashboardMetricsProvider;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Override;

class DashboardServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Dashboard';

    protected string $nameLower = 'dashboard';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    #[Override]
    public function register(): void
    {
        parent::register();

        $this->app->singleton(DashboardMetricsProvider::class, GetAuthCounts::class);
    }
}
