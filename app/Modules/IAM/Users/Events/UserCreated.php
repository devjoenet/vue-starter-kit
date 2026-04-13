<?php

declare(strict_types=1);

namespace App\Modules\IAM\Users\Events;

use App\Modules\Shared\Events\AbstractAuditableEvent;
use App\Modules\Shared\Models\User;

final readonly class UserCreated extends AbstractAuditableEvent
{
    /** @param  array{name: string, email: string}|null  $before */
    public function __construct(User $user, ?array $before, bool $restored)
    {
        parent::__construct(
            event: $restored ? 'users.restored' : 'users.created',
            summary: ($restored ? 'Restored' : 'Created').sprintf(' user %s.', $user->email),
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
