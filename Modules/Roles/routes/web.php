<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Roles\Http\Controllers\RolesController;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/roles')
    ->name('admin.roles.')
    ->group(function (): void {
        Route::get('/', [RolesController::class, 'index'])->name('index');
        Route::get('/create', [RolesController::class, 'create'])->name('create');
        Route::post('/', [RolesController::class, 'store'])->name('store');
        Route::get('/{role}/edit', [RolesController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RolesController::class, 'update'])->name('update');
        Route::delete('/{role}', [RolesController::class, 'destroy'])->name('destroy');
        Route::put('/{role}/permissions', [RolesController::class, 'syncPermissions'])->name('permissions.sync');
    });
