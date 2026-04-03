<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->index(['deleted_at', 'name'], 'users_deleted_at_name_index');
            $table->index(['deleted_at', 'email'], 'users_deleted_at_email_index');
        });

        Schema::table('roles', function (Blueprint $table): void {
            $table->index(['deleted_at', 'name'], 'roles_deleted_at_name_index');
        });

        Schema::table('permission_groups', function (Blueprint $table): void {
            $table->index(['deleted_at', 'label', 'slug'], 'permission_groups_deleted_at_label_slug_index');
        });

        Schema::table('permissions', function (Blueprint $table): void {
            $table->index(['deleted_at', 'label'], 'permissions_deleted_at_label_index');
            $table->index(['deleted_at', 'name'], 'permissions_deleted_at_name_index');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table): void {
            $table->dropIndex('permissions_deleted_at_name_index');
            $table->dropIndex('permissions_deleted_at_label_index');
        });

        Schema::table('permission_groups', function (Blueprint $table): void {
            $table->dropIndex('permission_groups_deleted_at_label_slug_index');
        });

        Schema::table('roles', function (Blueprint $table): void {
            $table->dropIndex('roles_deleted_at_name_index');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('users_deleted_at_email_index');
            $table->dropIndex('users_deleted_at_name_index');
        });
    }
};
