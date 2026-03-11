<?php

declare(strict_types=1);

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Inertia\ExceptionResponse;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Inertia::handleExceptionsUsing(function (ExceptionResponse $response) {
            if ($response->request->expectsJson()) {
                return null;
            }

            if (! in_array($response->statusCode(), [403, 404, 500, 503], true)) {
                return null;
            }

            return $response
                ->render('ErrorPage', [
                    'status' => $response->statusCode(),
                ])
                ->withSharedData();
        });
    })->create();
