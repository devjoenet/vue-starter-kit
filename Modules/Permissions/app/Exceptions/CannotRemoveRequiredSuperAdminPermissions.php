<?php

declare(strict_types=1);

namespace Modules\Permissions\Exceptions;

use Modules\Core\Exceptions\AbstractIamValidationException;
use Modules\Roles\Actions\EnsureSuperAdminRole;
use Modules\Roles\Models\Role;

final class CannotRemoveRequiredSuperAdminPermissions extends AbstractIamValidationException
{
    /** @param  list<string>  $permissionNames */
    /** @param  list<string>  $requiredPermissionNames */
    private function __construct(
        public readonly int $roleId,
        public readonly string $roleName,
        public readonly array $permissionNames,
        public readonly array $requiredPermissionNames,
    ) {
        parent::__construct(
            errors: ['permissions' => [$this->validationMessage()]],
            context: [
                'role_id' => $this->roleId,
                'role_name' => $this->roleName,
                'permission_names' => $this->permissionNames,
                'required_permission_names' => $this->requiredPermissionNames,
                'protected_role' => EnsureSuperAdminRole::name(),
            ],
        );
    }

    /** @param  list<string>  $permissionNames */
    /** @param  list<string>  $requiredPermissionNames */
    public static function forRole(Role $role, array $permissionNames, array $requiredPermissionNames): self
    {
        return new self(
            roleId: (int) $role->getKey(),
            roleName: $role->name,
            permissionNames: $permissionNames,
            requiredPermissionNames: $requiredPermissionNames,
        );
    }

    public function validationMessage(): string
    {
        return 'The protected super-admin role must keep every permission.';
    }
}
