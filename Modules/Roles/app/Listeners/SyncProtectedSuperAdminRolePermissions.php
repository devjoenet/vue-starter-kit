<?php

declare(strict_types=1);

namespace Modules\Roles\Listeners;

use Modules\Permissions\Events\PermissionCreated;
use Modules\Permissions\Events\PermissionDeleted;
use Modules\Roles\Actions\EnsureSuperAdminRole;

final class SyncProtectedSuperAdminRolePermissions
{
    /** Keep the typed event parameter so Laravel can auto-discover this listener. */
    public function handle(PermissionCreated|PermissionDeleted $event): void
    {
        if (! str_starts_with($event->auditEvent(), 'permissions.')) {
            return;
        }

        EnsureSuperAdminRole::handle();
    }
}
