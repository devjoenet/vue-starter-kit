<?php

declare(strict_types=1);

use App\Enums\AdminPermission;
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
                    ->middleware('can:'.AdminPermission::UsersView->value)
                    ->name('index');

                Route::get('/create', [UsersController::class, 'create'])
                    ->middleware('can:'.AdminPermission::UsersCreate->value)
                    ->name('create');

                Route::post('/', [UsersController::class, 'store'])
                    ->middleware('can:'.AdminPermission::UsersCreate->value)
                    ->name('store');

                Route::get('/{user}/edit', [UsersController::class, 'edit'])
                    ->middleware('can:'.AdminPermission::UsersUpdate->value)
                    ->name('edit');

                Route::put('/{user}', [UsersController::class, 'update'])
                    ->middleware('can:'.AdminPermission::UsersUpdate->value)
                    ->name('update');

                Route::delete('/{user}', [UsersController::class, 'destroy'])
                    ->middleware('can:'.AdminPermission::UsersDelete->value)
                    ->name('destroy');

                Route::put('/{user}/roles', [UsersController::class, 'syncRoles'])
                    ->middleware('can:'.AdminPermission::UsersAssignRoles->value)
                    ->name('roles.sync');
            });

        Route::prefix('roles')
            ->name('roles.')
            ->group(function (): void {
                Route::get('/', [RolesController::class, 'index'])
                    ->middleware('can:'.AdminPermission::RolesView->value)
                    ->name('index');

                Route::get('/create', [RolesController::class, 'create'])
                    ->middleware('can:'.AdminPermission::RolesCreate->value)
                    ->name('create');

                Route::post('/', [RolesController::class, 'store'])
                    ->middleware('can:'.AdminPermission::RolesCreate->value)
                    ->name('store');

                Route::get('/{role}/edit', [RolesController::class, 'edit'])
                    ->middleware('can:'.AdminPermission::RolesUpdate->value)
                    ->name('edit');

                Route::put('/{role}', [RolesController::class, 'update'])
                    ->middleware('can:'.AdminPermission::RolesUpdate->value)
                    ->name('update');

                Route::delete('/{role}', [RolesController::class, 'destroy'])
                    ->middleware('can:'.AdminPermission::RolesDelete->value)
                    ->name('destroy');

                Route::put('/{role}/permissions', [RolesController::class, 'syncPermissions'])
                    ->middleware('can:'.AdminPermission::RolesAssignPermissions->value)
                    ->name('permissions.sync');
            });

        Route::prefix('permissions')
            ->name('permissions.')
            ->group(function (): void {
                Route::get('/', [PermissionsController::class, 'index'])
                    ->middleware('can:'.AdminPermission::PermissionsView->value)
                    ->name('index');

                Route::get('/create', [PermissionsController::class, 'create'])
                    ->middleware('can:'.AdminPermission::PermissionsCreate->value)
                    ->name('create');

                Route::post('/', [PermissionsController::class, 'store'])
                    ->middleware('can:'.AdminPermission::PermissionsCreate->value)
                    ->name('store');

                Route::get('/{permission}/edit', [PermissionsController::class, 'edit'])
                    ->middleware('can:'.AdminPermission::PermissionsUpdate->value)
                    ->name('edit');

                Route::put('/{permission}', [PermissionsController::class, 'update'])
                    ->middleware('can:'.AdminPermission::PermissionsUpdate->value)
                    ->name('update');

                Route::delete('/{permission}', [PermissionsController::class, 'destroy'])
                    ->middleware('can:'.AdminPermission::PermissionsDelete->value)
                    ->name('destroy');
            });
    });
