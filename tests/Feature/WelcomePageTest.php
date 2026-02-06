<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;

test('welcome page can be rendered', function () {
    $response = $this->get(route('home'));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->has('canRegister')
    );
});
