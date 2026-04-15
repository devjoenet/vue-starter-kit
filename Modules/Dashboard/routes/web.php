<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\Dashboard\Actions\GetDashboardSources;
use Modules\Dashboard\Contracts\DashboardMetricsProvider;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))->name('dashboard');

    Route::prefix('admin')
        ->name('admin.')
        ->group(function (): void {
            Route::get('/', fn (DashboardMetricsProvider $dashboardMetricsProvider) => Inertia::render('Dashboard/Index', [
                'sources' => GetDashboardSources::handle($dashboardMetricsProvider),
            ]))->name('dashboard');
        });
});
