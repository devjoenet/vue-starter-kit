<?php

declare(strict_types=1);

namespace App\Modules\IAM\Exceptions;

use App\Modules\IAM\Actions\EnsureSuperAdminRole;
use App\Modules\IAM\Models\Role;

final class CannotRenameProtectedSuperAdminRole extends AbstractIamValidationException
{
    private function __construct(
        public readonly int $roleId,
        public readonly string $currentName,
        public readonly string $requestedName,
    ) {
        parent::__construct(
            errors: ['name' => [$this->validationMessage()]],
            context: [
                'role_id' => $this->roleId,
                'current_name' => $this->currentName,
                'requested_name' => $this->requestedName,
                'protected_role' => EnsureSuperAdminRole::name(),
            ],
        );
    }

    public static function forRole(Role $role, string $requestedName): self
    {
        return new self(
            roleId: (int) $role->getKey(),
            currentName: $role->name,
            requestedName: $requestedName,
        );
    }

    public function validationMessage(): string
    {
        return 'The protected super-admin role name is reserved and cannot be reassigned.';
    }
}
