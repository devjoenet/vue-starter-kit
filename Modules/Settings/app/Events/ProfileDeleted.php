<?php

declare(strict_types=1);

namespace Modules\Settings\Events;

use Modules\Core\Events\AbstractAuditableEvent;
use Modules\Core\Models\User;

final readonly class ProfileDeleted extends AbstractAuditableEvent
{
    /** @param  array{name: string, email: string}  $before */
    public function __construct(User $user, array $before)
    {
        parent::__construct(
            event: 'settings.profile_deleted',
            summary: sprintf('Deleted profile for %s.', $user->email),
            subjectType: $user::class,
            subjectId: (int) $user->getKey(),
            subjectLabel: $user->email,
            changes: ['before' => $before, 'after' => null],
        );
    }
}
