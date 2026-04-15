<?php

declare(strict_types=1);

namespace App\Providers;

use App\Inertia\ModulePageFinder;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Foundation\Application;
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
        $this->app->bind('inertia.view-finder', fn (Application $app): ModulePageFinder => new ModulePageFinder(
            $app['files'],
            $app['config']->get('inertia.pages.paths'),
            $app['config']->get('inertia.pages.extensions'),
        ));
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
