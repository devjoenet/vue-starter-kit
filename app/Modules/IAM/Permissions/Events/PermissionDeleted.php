<?php

declare(strict_types=1);

namespace App\Modules\IAM\Permissions\Events;

use App\Modules\IAM\Permissions\Models\Permission;
use App\Modules\Shared\Events\AbstractAuditableEvent;

final readonly class PermissionDeleted extends AbstractAuditableEvent
{
    /** @param  array{name: string, label: string|null, description: string|null, group: string, group_label: string}  $before */
    public function __construct(Permission $permission, array $before)
    {
        parent::__construct(
            event: 'permissions.deleted',
            summary: sprintf('Deleted permission %s.', $permission->name),
            subjectType: $permission::class,
            subjectId: (int) $permission->getKey(),
            subjectLabel: $permission->name,
            changes: ['before' => $before, 'after' => null],
        );
    }
}
