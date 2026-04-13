<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Listeners;

use App\Modules\IAM\Permissions\Events\PermissionCreated;
use App\Modules\IAM\Permissions\Events\PermissionDeleted;
use App\Modules\IAM\Roles\Actions\EnsureSuperAdminRole;

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
