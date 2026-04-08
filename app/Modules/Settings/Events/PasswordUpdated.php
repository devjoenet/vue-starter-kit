<?php

declare(strict_types=1);

namespace App\Modules\Settings\Events;

use App\Modules\Shared\Events\AbstractAuditableEvent;
use App\Modules\Shared\Models\User;

final readonly class PasswordUpdated extends AbstractAuditableEvent
{
    public function __construct(User $user)
    {
        parent::__construct(
            event: 'settings.password_updated',
            summary: sprintf('Updated password for %s.', $user->email),
            subjectType: $user::class,
            subjectId: (int) $user->getKey(),
            subjectLabel: $user->email,
            context: ['password_updated' => true],
        );
    }
}
