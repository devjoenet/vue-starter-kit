<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Exceptions;

use App\Modules\IAM\Exceptions\AbstractIamValidationException;
use App\Modules\IAM\Roles\Actions\EnsureSuperAdminRole;
use App\Modules\Shared\Models\User;

final class CannotRemoveLastSuperAdminRoleAssignment extends AbstractIamValidationException
{
    /** @param  list<string>  $nextRoleNames */
    private function __construct(
        public readonly int $userId,
        public readonly string $email,
        public readonly array $nextRoleNames,
    ) {
        parent::__construct(
            errors: ['roles' => [$this->validationMessage()]],
            context: [
                'user_id' => $this->userId,
                'email' => $this->email,
                'next_role_names' => $this->nextRoleNames,
                'protected_role' => EnsureSuperAdminRole::name(),
            ],
        );
    }

    /** @param  list<string>  $nextRoleNames */
    public static function forUser(User $user, array $nextRoleNames): self
    {
        return new self(
            userId: (int) $user->getKey(),
            email: $user->email,
            nextRoleNames: $nextRoleNames,
        );
    }

    public function validationMessage(): string
    {
        return 'The last active super-admin user must keep the super-admin role.';
    }
}
