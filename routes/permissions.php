<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');

        // Users
        Route::get('/users', [UsersController::class, 'index'])
            ->middleware('can:users.view')
            ->name('users.index');

        Route::get('/users/create', [UsersController::class, 'create'])
            ->middleware('can:users.create')
            ->name('users.create');

        Route::post('/users', [UsersController::class, 'store'])
            ->middleware('can:users.create')
            ->name('users.store');

        Route::get('/users/{user}/edit', [UsersController::class, 'edit'])
            ->middleware('can:users.update')
            ->name('users.edit');

        Route::put('/users/{user}', [UsersController::class, 'update'])
            ->middleware('can:users.update')
            ->name('users.update');

        Route::delete('/users/{user}', [UsersController::class, 'destroy'])
            ->middleware('can:users.delete')
            ->name('users.destroy');

        Route::put('/users/{user}/roles', [UsersController::class, 'syncRoles'])
            ->middleware('can:users.assignRoles')
            ->name('users.roles.sync');

        // Roles
        Route::get('/roles', [RolesController::class, 'index'])
            ->middleware('can:roles.view')
            ->name('roles.index');

        Route::get('/roles/create', [RolesController::class, 'create'])
            ->middleware('can:roles.create')
            ->name('roles.create');

        Route::post('/roles', [RolesController::class, 'store'])
            ->middleware('can:roles.create')
            ->name('roles.store');

        Route::get('/roles/{role}/edit', [RolesController::class, 'edit'])
            ->middleware('can:roles.update')
            ->name('roles.edit');

        Route::put('/roles/{role}', [RolesController::class, 'update'])
            ->middleware('can:roles.update')
            ->name('roles.update');

        Route::delete('/roles/{role}', [RolesController::class, 'destroy'])
            ->middleware('can:roles.delete')
            ->name('roles.destroy');

        Route::put('/roles/{role}/permissions', [RolesController::class, 'syncPermissions'])
            ->middleware('can:roles.assignPermissions')
            ->name('roles.permissions.sync');

        // Permissions
        Route::get('/permissions', [PermissionsController::class, 'index'])
            ->middleware('can:permissions.view')
            ->name('permissions.index');

        Route::get('/permissions/create', [PermissionsController::class, 'create'])
            ->middleware('can:permissions.create')
            ->name('permissions.create');

        Route::post('/permissions', [PermissionsController::class, 'store'])
            ->middleware('can:permissions.create')
            ->name('permissions.store');

        Route::get('/permissions/{permission}/edit', [PermissionsController::class, 'edit'])
            ->middleware('can:permissions.update')
            ->name('permissions.edit');

        Route::put('/permissions/{permission}', [PermissionsController::class, 'update'])
            ->middleware('can:permissions.update')
            ->name('permissions.update');

        Route::delete('/permissions/{permission}', [PermissionsController::class, 'destroy'])
            ->middleware('can:permissions.delete')
            ->name('permissions.destroy');
    });
