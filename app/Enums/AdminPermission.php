<?php

declare(strict_types=1);

namespace App\Enums;

enum AdminPermission: string
{
    // Users
    case UsersView = 'users.view';
    case UsersCreate = 'users.create';
    case UsersUpdate = 'users.update';
    case UsersDelete = 'users.delete';
    case UsersAssignRoles = 'users.assignRoles';

    // Roles
    case RolesView = 'roles.view';
    case RolesCreate = 'roles.create';
    case RolesUpdate = 'roles.update';
    case RolesDelete = 'roles.delete';
    case RolesAssignPermissions = 'roles.assignPermissions';

    // Permissions
    case PermissionsView = 'permissions.view';
    case PermissionsCreate = 'permissions.create';
    case PermissionsUpdate = 'permissions.update';
    case PermissionsDelete = 'permissions.delete';

    public function group(): string
    {
        return match ($this) {
            self::UsersView,
            self::UsersCreate,
            self::UsersUpdate,
            self::UsersDelete,
            self::UsersAssignRoles => 'users',

            self::RolesView,
            self::RolesCreate,
            self::RolesUpdate,
            self::RolesDelete,
            self::RolesAssignPermissions => 'roles',

            self::PermissionsView,
            self::PermissionsCreate,
            self::PermissionsUpdate,
            self::PermissionsDelete => 'permissions',
        };
    }
}
