<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

it('creates the test user with the super-admin role outside production', function () {
    runDatabaseSeeder();

    $user = User::query()
        ->where('email', 'test@example.com')
        ->firstOrFail();

    expect($user->name)->toBe('Test User');
    expect($user->hasRole('super-admin'))->toBeTrue();
});

it('skips the test user when seeding in production', function () {
    $originalEnvironment = app()->environment();
    app()['env'] = 'production';

    try {
        runDatabaseSeeder();
    } finally {
        app()['env'] = $originalEnvironment;
    }

    expect(User::query()->where('email', 'test@example.com')->exists())->toBeFalse();
});

function runDatabaseSeeder(): void
{
    $seeder = new DatabaseSeeder();
    $seeder->setContainer(app());
    $seeder->__invoke();
}
