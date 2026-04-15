<?php

declare(strict_types=1);

namespace Modules\Users\Exceptions;

use Modules\Core\Exceptions\AbstractIamValidationException;
use Modules\Core\Models\User;
use Modules\Roles\Actions\EnsureSuperAdminRole;

final class CannotDeleteLastSuperAdminUser extends AbstractIamValidationException
{
    private function __construct(
        public readonly int $userId,
        public readonly string $email,
    ) {
        parent::__construct(
            errors: ['user' => [$this->validationMessage()]],
            context: [
                'user_id' => $this->userId,
                'email' => $this->email,
                'protected_role' => EnsureSuperAdminRole::name(),
            ],
        );
    }

    public static function forUser(User $user): self
    {
        return new self(
            userId: (int) $user->getKey(),
            email: $user->email,
        );
    }

    public function validationMessage(): string
    {
        return 'The last active super-admin user cannot be deleted.';
    }
}
