<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Permissions\Http\Controllers\PermissionsController;

Route::middleware(['auth', 'verified'])
    ->prefix('admin/permissions')
    ->name('admin.permissions.')
    ->group(function (): void {
        Route::get('/', [PermissionsController::class, 'index'])->name('index');
        Route::get('/create', [PermissionsController::class, 'create'])->name('create');
        Route::post('/', [PermissionsController::class, 'store'])->name('store');
        Route::get('/{permission}/edit', [PermissionsController::class, 'edit'])->name('edit');
        Route::put('/{permission}', [PermissionsController::class, 'update'])->name('update');
        Route::delete('/{permission}', [PermissionsController::class, 'destroy'])->name('destroy');
    });
