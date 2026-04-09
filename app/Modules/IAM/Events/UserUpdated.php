<?php

declare(strict_types=1);

namespace App\Modules\IAM\Events;

use App\Modules\Shared\Events\AbstractAuditableEvent;
use App\Modules\Shared\Models\User;

final readonly class UserUpdated extends AbstractAuditableEvent
{
    /** @param  array{name: string, email: string}  $before */
    public function __construct(User $user, array $before, bool $passwordUpdated)
    {
        parent::__construct(
            event: 'users.updated',
            summary: sprintf('Updated user %s.', $user->email),
            subjectType: $user::class,
            subjectId: (int) $user->getKey(),
            subjectLabel: $user->email,
            changes: ['before' => $before, 'after' => $this->auditState($user)],
            context: ['password_updated' => $passwordUpdated],
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
