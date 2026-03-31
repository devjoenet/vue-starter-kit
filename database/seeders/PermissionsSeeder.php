<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\PermissionGroup;
use Illuminate\Database\Seeder;

final class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = now();
        $groupIds = PermissionGroup::query()->pluck('id', 'slug');

        Permission::query()->upsert([
            [
                'name' => 'users.view',
                'label' => 'View Users',
                'description' => 'Browse user records and open the user management workspace.',
                'permission_group_id' => $groupIds->get('users'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'users.create',
                'label' => 'Create Users',
                'description' => 'Create new user accounts for staff or internal operators.',
                'permission_group_id' => $groupIds->get('users'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'users.update',
                'label' => 'Update Users',
                'description' => 'Edit user identities, contact details, and account settings.',
                'permission_group_id' => $groupIds->get('users'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'users.delete',
                'label' => 'Delete Users',
                'description' => 'Delete user accounts from the application.',
                'permission_group_id' => $groupIds->get('users'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'users.assignRoles',
                'label' => 'Assign User Roles',
                'description' => 'Attach or remove roles from user accounts.',
                'permission_group_id' => $groupIds->get('users'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'roles.view',
                'label' => 'View Roles',
                'description' => 'Browse roles and inspect their access footprints.',
                'permission_group_id' => $groupIds->get('roles'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'roles.create',
                'label' => 'Create Roles',
                'description' => 'Create new roles for internal teams or other actors.',
                'permission_group_id' => $groupIds->get('roles'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'roles.update',
                'label' => 'Update Roles',
                'description' => 'Rename roles and maintain their assignment footprint.',
                'permission_group_id' => $groupIds->get('roles'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'roles.delete',
                'label' => 'Delete Roles',
                'description' => 'Delete roles that are no longer needed.',
                'permission_group_id' => $groupIds->get('roles'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'roles.assignPermissions',
                'label' => 'Assign Role Permissions',
                'description' => 'Change the permissions that belong to a role.',
                'permission_group_id' => $groupIds->get('roles'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'permissions.view',
                'label' => 'View Permissions',
                'description' => 'Browse the permission catalog and open permission editors.',
                'permission_group_id' => $groupIds->get('permissions'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'permissions.create',
                'label' => 'Create Permissions',
                'description' => 'Add new permissions to the shared access-control catalog.',
                'permission_group_id' => $groupIds->get('permissions'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'permissions.update',
                'label' => 'Update Permissions',
                'description' => 'Maintain permission labels, descriptions, and grouping metadata.',
                'permission_group_id' => $groupIds->get('permissions'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'permissions.delete',
                'label' => 'Delete Permissions',
                'description' => 'Delete permissions from the shared catalog.',
                'permission_group_id' => $groupIds->get('permissions'),
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ], ['name', 'guard_name'], [
            'label',
            'description',
            'permission_group_id',
            'deleted_at',
            'updated_at',
        ]);
    }
}
