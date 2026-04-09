<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\IAM\Models\PermissionGroup;
use Illuminate\Database\Seeder;

final class PermissionGroupsSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = now();

        PermissionGroup::query()->upsert([
            [
                'slug' => 'users',
                'label' => 'User Administration',
                'description' => 'Identity, lifecycle, and role-assignment access for internal users.',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'slug' => 'roles',
                'label' => 'Role Management',
                'description' => 'Role creation, maintenance, and permission-footprint controls.',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'slug' => 'permissions',
                'label' => 'Permission Catalog',
                'description' => 'Permission definitions, grouping, and ACL catalog maintenance.',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'slug' => 'audit_logs',
                'label' => 'Audit Logs',
                'description' => 'Read-only traceability access for the operator audit trail.',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ], ['slug'], ['label', 'description', 'deleted_at', 'updated_at']);
    }
}
