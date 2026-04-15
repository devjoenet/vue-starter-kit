<?php

declare(strict_types=1);

namespace Modules\Roles\Events;

use Modules\Core\Events\AbstractAuditableEvent;
use Modules\Roles\Models\Role;

final readonly class RolePermissionsSynced extends AbstractAuditableEvent
{
    /**
     * @param  list<string>  $beforePermissions
     * @param  list<string>  $afterPermissions
     */
    public function __construct(Role $role, array $beforePermissions, array $afterPermissions)
    {
        parent::__construct(
            event: 'roles.permissions_synced',
            summary: sprintf('Updated permissions for role %s.', $role->name),
            subjectType: $role::class,
            subjectId: (int) $role->getKey(),
            subjectLabel: $role->name,
            changes: ['before' => ['permissions' => $beforePermissions], 'after' => ['permissions' => $afterPermissions]],
        );
    }
}
