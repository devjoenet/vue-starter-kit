<?php

declare(strict_types=1);

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
            ->where('sidebarOpen', true)
            ->has('flash')
        );
});
