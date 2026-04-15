<?php

declare(strict_types=1);

namespace Modules\Roles\Events;

use Modules\Core\Events\AbstractAuditableEvent;
use Modules\Roles\Models\Role;

final readonly class RoleDeleted extends AbstractAuditableEvent
{
    /** @param  array{name: string}  $before */
    public function __construct(Role $role, array $before)
    {
        parent::__construct(
            event: 'roles.deleted',
            summary: sprintf('Deleted role %s.', $role->name),
            subjectType: $role::class,
            subjectId: (int) $role->getKey(),
            subjectLabel: $role->name,
            changes: ['before' => $before, 'after' => null],
        );
    }
}
