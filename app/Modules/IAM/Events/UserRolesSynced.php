<?php

declare(strict_types=1);

namespace App\Modules\IAM\Events;

use App\Modules\Shared\Events\AbstractAuditableEvent;
use App\Modules\Shared\Models\User;

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
