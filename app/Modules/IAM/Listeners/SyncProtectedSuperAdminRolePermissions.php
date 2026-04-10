<?php

declare(strict_types=1);

namespace App\Modules\IAM\Listeners;

use App\Modules\IAM\Actions\EnsureSuperAdminRole;
use App\Modules\IAM\Events\PermissionCreated;
use App\Modules\IAM\Events\PermissionDeleted;

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
