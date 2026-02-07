<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('admin.dashboard'));
});

test('authenticated users can visit the admin dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Dashboard')
            ->has('counts', fn (Assert $counts) => $counts
                ->has('users')
                ->has('roles')
                ->has('permissions')
            )
        );
});
