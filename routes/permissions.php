<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuditLogsController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use App\Modules\Dashboard\Actions\GetDashboardSources;
use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', fn (DashboardMetricsProvider $dashboardMetricsProvider) => Inertia::render('admin/Dashboard', [
            'sources' => GetDashboardSources::handle($dashboardMetricsProvider),
        ]))->name('dashboard');

        Route::get('/audit-logs', [AuditLogsController::class, 'index'])->name('audit-logs.index');

        Route::prefix('users')
            ->name('users.')
            ->group(function (): void {
                Route::get('/', [UsersController::class, 'index'])->name('index');

                Route::get('/create', [UsersController::class, 'create'])->name('create');

                Route::post('/', [UsersController::class, 'store'])->name('store');

                Route::get('/{user}/edit', [UsersController::class, 'edit'])->name('edit');

                Route::put('/{user}', [UsersController::class, 'update'])->name('update');

                Route::delete('/{user}', [UsersController::class, 'destroy'])->name('destroy');

                Route::put('/{user}/roles', [UsersController::class, 'syncRoles'])->name('roles.sync');
            });

        Route::prefix('roles')
            ->name('roles.')
            ->group(function (): void {
                Route::get('/', [RolesController::class, 'index'])->name('index');

                Route::get('/create', [RolesController::class, 'create'])->name('create');

                Route::post('/', [RolesController::class, 'store'])->name('store');

                Route::get('/{role}/edit', [RolesController::class, 'edit'])->name('edit');

                Route::put('/{role}', [RolesController::class, 'update'])->name('update');

                Route::delete('/{role}', [RolesController::class, 'destroy'])->name('destroy');

                Route::put('/{role}/permissions', [RolesController::class, 'syncPermissions'])->name('permissions.sync');
            });

        Route::prefix('permissions')
            ->name('permissions.')
            ->group(function (): void {
                Route::get('/', [PermissionsController::class, 'index'])->name('index');

                Route::get('/create', [PermissionsController::class, 'create'])->name('create');

                Route::post('/', [PermissionsController::class, 'store'])->name('store');

                Route::get('/{permission}/edit', [PermissionsController::class, 'edit'])->name('edit');

                Route::put('/{permission}', [PermissionsController::class, 'update'])->name('update');

                Route::delete('/{permission}', [PermissionsController::class, 'destroy'])->name('destroy');
            });
    });
