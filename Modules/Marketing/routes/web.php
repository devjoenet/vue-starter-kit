<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Marketing/Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');
