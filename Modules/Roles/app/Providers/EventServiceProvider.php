<?php

declare(strict_types=1);

namespace Modules\Roles\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Permissions\Events\PermissionCreated;
use Modules\Permissions\Events\PermissionDeleted;
use Modules\Roles\Listeners\SyncProtectedSuperAdminRolePermissions;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PermissionCreated::class => [
            SyncProtectedSuperAdminRolePermissions::class,
        ],
        PermissionDeleted::class => [
            SyncProtectedSuperAdminRolePermissions::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
