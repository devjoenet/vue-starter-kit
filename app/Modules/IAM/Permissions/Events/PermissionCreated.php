<?php

declare(strict_types=1);

namespace App\Modules\IAM\Permissions\Events;

use App\Modules\IAM\Permissions\Models\Permission;
use App\Modules\Shared\Events\AbstractAuditableEvent;

final readonly class PermissionCreated extends AbstractAuditableEvent
{
    /** @param  array{name: string, label: string|null, description: string|null, group: string, group_label: string}|null  $before */
    public function __construct(Permission $permission, ?array $before, bool $restored)
    {
        parent::__construct(
            event: $restored ? 'permissions.restored' : 'permissions.created',
            summary: ($restored ? 'Restored' : 'Created').sprintf(' permission %s.', $permission->name),
            subjectType: $permission::class,
            subjectId: (int) $permission->getKey(),
            subjectLabel: $permission->name,
            changes: ['before' => $before, 'after' => $this->auditState($permission)],
        );
    }

    /** @return array{name: string, label: string|null, description: string|null, group: string, group_label: string} */
    private function auditState(Permission $permission): array
    {
        return [
            'name' => $permission->name,
            'label' => $permission->label,
            'description' => $permission->description,
            'group' => $permission->group,
            'group_label' => $permission->group_label,
        ];
    }
}
