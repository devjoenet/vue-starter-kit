<?php

declare(strict_types=1);

namespace App\Providers;

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
use Carbon\CarbonImmutable;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Override;

class AppServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->singleton(DashboardMetricsProvider::class, DashboardMetrics::class);
        $this->app->singleton(PermissionGroupCatalogContract::class, PermissionGroupCatalog::class);
        $this->app->singleton(PermissionFilterOptionsProvider::class, PermissionFilterOptionsCatalog::class);
        $this->app->singleton(GroupedPermissionsProvider::class, GroupedPermissions::class);
        $this->app->singleton(RoleFilterOptionsProvider::class, RoleFilterOptionsCatalog::class);
        $this->app->singleton(UserFilterOptionsProvider::class, UserFilterOptionsCatalog::class);
    }

    public function boot(): void
    {
        $this->configureDefaults();

        Vite::prefetch(concurrency: (int) config('performance.observability.vite_prefetch_concurrency', 3));

        DB::whenQueryingForLongerThan(
            config('performance.observability.slow_query_threshold_ms', 120),
            static function (Connection $connection, QueryExecuted $event): void {
                logger()->warning('Slow query budget exceeded.', [
                    'connection' => $connection->getName(),
                    'duration_ms' => $event->time,
                    'total_duration_ms' => $connection->totalQueryDuration(),
                    'sql' => $event->sql,
                    'bindings_count' => count($event->bindings),
                ]);
            },
        );
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
