<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Events;

use App\Modules\IAM\Roles\Models\Role;
use App\Modules\Shared\Events\AbstractAuditableEvent;

final readonly class RoleUpdated extends AbstractAuditableEvent
{
    /** @param  array{name: string}  $before */
    public function __construct(Role $role, array $before)
    {
        parent::__construct(
            event: 'roles.updated',
            summary: sprintf('Updated role %s.', $role->name),
            subjectType: $role::class,
            subjectId: (int) $role->getKey(),
            subjectLabel: $role->name,
            changes: ['before' => $before, 'after' => ['name' => $role->name]],
        );
    }
}
