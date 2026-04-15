<?php

declare(strict_types=1);

namespace Modules\Roles\Exceptions;

use Modules\Core\Exceptions\AbstractIamValidationException;
use Modules\Roles\Actions\EnsureSuperAdminRole;
use Modules\Roles\Models\Role;

final class CannotDeleteProtectedSuperAdminRole extends AbstractIamValidationException
{
    private function __construct(
        public readonly int $roleId,
        public readonly string $roleName,
    ) {
        parent::__construct(
            errors: ['role' => [$this->validationMessage()]],
            context: [
                'role_id' => $this->roleId,
                'role_name' => $this->roleName,
                'protected_role' => EnsureSuperAdminRole::name(),
            ],
        );
    }

    public static function forRole(Role $role): self
    {
        return new self(
            roleId: (int) $role->getKey(),
            roleName: $role->name,
        );
    }

    public function validationMessage(): string
    {
        return 'The protected super-admin role cannot be deleted.';
    }
}
