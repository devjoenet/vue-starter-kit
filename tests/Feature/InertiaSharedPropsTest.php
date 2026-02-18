<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;

test('login page includes shared flash props', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/Login')
            ->has('flash')
            ->where('flash.success', null)
            ->where('flash.error', null)
            ->where('flash.warning', null)
            ->where('flash.info', null)
        );
});

test('shared flash success is available to inertia pages', function () {
    $this->withSession(['success' => 'Everything saved.'])
        ->get(route('login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/Login')
            ->where('flash.success', 'Everything saved.')
        );
});

test('shared flash warning and info are available to inertia pages', function () {
    $this->withSession([
        'warning' => 'Double-check this action.',
        'info' => 'Background sync started.',
    ])
        ->get(route('login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/Login')
            ->where('flash.warning', 'Double-check this action.')
            ->where('flash.info', 'Background sync started.')
        );
});
