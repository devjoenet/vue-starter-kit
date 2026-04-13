<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\IAM\Roles\Models\Role;
use Illuminate\Database\Seeder;

final class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = now();

        Role::query()->upsert([
            [
                'name' => 'super-admin',
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'user',
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'guest',
                'guard_name' => 'web',
                'deleted_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ], ['name', 'guard_name'], ['deleted_at', 'updated_at']);
    }
}
