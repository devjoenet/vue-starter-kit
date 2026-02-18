<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', fn () => Inertia::render('admin/Dashboard', [
            'counts' => [
                'users' => User::query()->count(),
                'roles' => Role::query()->count(),
                'permissions' => Permission::query()->count(),
            ],
        ]))->name('dashboard');

        Route::prefix('users')
            ->name('users.')
            ->group(function (): void {
                Route::get('/', [UsersController::class, 'index'])
                    ->middleware('can:users.view')
                    ->name('index');

                Route::get('/create', [UsersController::class, 'create'])
                    ->middleware('can:users.create')
                    ->name('create');

                Route::post('/', [UsersController::class, 'store'])
                    ->middleware('can:users.create')
                    ->name('store');

                Route::get('/{user}/edit', [UsersController::class, 'edit'])
                    ->middleware('can:users.update')
                    ->name('edit');

                Route::put('/{user}', [UsersController::class, 'update'])
                    ->middleware('can:users.update')
                    ->name('update');

                Route::delete('/{user}', [UsersController::class, 'destroy'])
                    ->middleware('can:users.delete')
                    ->name('destroy');

                Route::put('/{user}/roles', [UsersController::class, 'syncRoles'])
                    ->middleware('can:users.assignRoles')
                    ->name('roles.sync');
            });

        Route::prefix('roles')
            ->name('roles.')
            ->group(function (): void {
                Route::get('/', [RolesController::class, 'index'])
                    ->middleware('can:roles.view')
                    ->name('index');

                Route::get('/create', [RolesController::class, 'create'])
                    ->middleware('can:roles.create')
                    ->name('create');

                Route::post('/', [RolesController::class, 'store'])
                    ->middleware('can:roles.create')
                    ->name('store');

                Route::get('/{role}/edit', [RolesController::class, 'edit'])
                    ->middleware('can:roles.update')
                    ->name('edit');

                Route::put('/{role}', [RolesController::class, 'update'])
                    ->middleware('can:roles.update')
                    ->name('update');

                Route::delete('/{role}', [RolesController::class, 'destroy'])
                    ->middleware('can:roles.delete')
                    ->name('destroy');

                Route::put('/{role}/permissions', [RolesController::class, 'syncPermissions'])
                    ->middleware('can:roles.assignPermissions')
                    ->name('permissions.sync');
            });

        Route::prefix('permissions')
            ->name('permissions.')
            ->group(function (): void {
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
    });
