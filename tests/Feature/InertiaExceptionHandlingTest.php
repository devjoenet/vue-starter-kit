<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

test('missing pages render through inertia with shared props', function () {
    $this->get('/this-page-does-not-exist')
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ErrorPage')
            ->where('status', 404)
            ->where('name', config('app.name'))
            ->where('auth', [
                'user' => null,
                'roles' => [],
                'permissions' => [],
            ])
            ->where('requestContext.id', fn (?string $requestId): bool => is_string($requestId) && $requestId !== '')
            ->where('sidebarOpen', true)
            ->has('flash')
        );
});

test('stale sessions render through inertia with a request reference', function () {
    // Arrange

    Route::middleware('web')->get('/testing/page-expired', function (): void {
        abort(419);
    });

    // Act

    $response = $this->get('/testing/page-expired');

    // Assert

    $response
        ->assertStatus(419)
        ->assertHeader('X-Request-Id')
        ->assertInertia(fn (Assert $page) => $page
            ->component('ErrorPage')
            ->where('status', 419)
            ->where('requestContext.id', $response->headers->get('X-Request-Id'))
        );
});
