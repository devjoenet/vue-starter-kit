<?php

declare(strict_types=1);

namespace Modules\Audit\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Audit\Listeners\RecordAuditableDomainEvent;
use Modules\Core\Contracts\AuditableDomainEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AuditableDomainEvent::class => [
            RecordAuditableDomainEvent::class,
        ],
    ];

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
