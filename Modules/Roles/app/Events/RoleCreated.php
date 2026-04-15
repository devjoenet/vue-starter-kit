<?php

declare(strict_types=1);

namespace Modules\Roles\Events;

use Modules\Core\Events\AbstractAuditableEvent;
use Modules\Roles\Models\Role;

final readonly class RoleCreated extends AbstractAuditableEvent
{
    /**
     * @param  array{name: string, user_ids: list<int>}|null  $before
     * @param  list<int>  $userIds
     */
    public function __construct(Role $role, ?array $before, array $userIds, bool $restored)
    {
        parent::__construct(
            event: $restored ? 'roles.restored' : 'roles.created',
            summary: ($restored ? 'Restored' : 'Created').sprintf(' role %s.', $role->name),
            subjectType: $role::class,
            subjectId: (int) $role->getKey(),
            subjectLabel: $role->name,
            changes: ['before' => $before, 'after' => $this->auditState($role, $userIds)],
        );
    }

    /**
     * @param  list<int>  $userIds
     * @return array{name: string, user_ids: list<int>}
     */
    private function auditState(Role $role, array $userIds): array
    {
        return [
            'name' => $role->name,
            'user_ids' => $userIds === []
                ? $role->users()->pluck('users.id')->sort()->values()->all()
                : $userIds,
        ];
    }
}
