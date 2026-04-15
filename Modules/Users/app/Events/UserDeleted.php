<?php

declare(strict_types=1);

namespace Modules\Users\Events;

use Modules\Core\Events\AbstractAuditableEvent;
use Modules\Core\Models\User;

final readonly class UserDeleted extends AbstractAuditableEvent
{
    /** @param  array{name: string, email: string}  $before */
    public function __construct(User $user, array $before)
    {
        parent::__construct(
            event: 'users.deleted',
            summary: sprintf('Deleted user %s.', $user->email),
            subjectType: $user::class,
            subjectId: (int) $user->getKey(),
            subjectLabel: $user->email,
            changes: ['before' => $before, 'after' => null],
        );
    }
}
