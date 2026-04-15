<?php

declare(strict_types=1);

namespace Modules\Users\Events;

use Modules\Core\Events\AbstractAuditableEvent;
use Modules\Core\Models\User;

final readonly class UserRolesSynced extends AbstractAuditableEvent
{
    /**
     * @param  list<string>  $beforeRoles
     * @param  list<string>  $afterRoles
     */
    public function __construct(User $user, array $beforeRoles, array $afterRoles)
    {
        parent::__construct(
            event: 'users.roles_synced',
            summary: sprintf('Updated roles for user %s.', $user->email),
            subjectType: $user::class,
            subjectId: (int) $user->getKey(),
            subjectLabel: $user->email,
            changes: ['before' => ['roles' => $beforeRoles], 'after' => ['roles' => $afterRoles]],
        );
    }
}
