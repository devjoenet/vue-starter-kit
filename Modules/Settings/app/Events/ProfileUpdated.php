<?php

declare(strict_types=1);

namespace Modules\Settings\Events;

use Modules\Core\Events\AbstractAuditableEvent;
use Modules\Core\Models\User;

final readonly class ProfileUpdated extends AbstractAuditableEvent
{
    /** @param  array{name: string, email: string}  $before */
    public function __construct(User $user, array $before)
    {
        parent::__construct(
            event: 'settings.profile_updated',
            summary: sprintf('Updated profile for %s.', $user->email),
            subjectType: $user::class,
            subjectId: (int) $user->getKey(),
            subjectLabel: $user->email,
            changes: ['before' => $before, 'after' => $this->auditState($user)],
        );
    }

    /** @return array{name: string, email: string} */
    private function auditState(User $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
