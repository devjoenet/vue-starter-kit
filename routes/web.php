<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

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
    });

Route::middleware(['auth', 'verified'])
    ->get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/permissions.php';
