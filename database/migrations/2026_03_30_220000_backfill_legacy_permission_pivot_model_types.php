<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private const string LegacyUserModelType = 'App\\Models\\User';

    private const string CurrentUserModelType = App\Modules\Shared\Models\User::class;

    public function up(): void
    {
        DB::transaction(function (): void {
            $this->backfillModelType(
                tableName: config('permission.table_names.model_has_roles', 'model_has_roles'),
                pivotColumn: 'role_id',
                fromModelType: self::LegacyUserModelType,
                toModelType: self::CurrentUserModelType,
            );

            $this->backfillModelType(
                tableName: config('permission.table_names.model_has_permissions', 'model_has_permissions'),
                pivotColumn: 'permission_id',
                fromModelType: self::LegacyUserModelType,
                toModelType: self::CurrentUserModelType,
            );
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            $this->backfillModelType(
                tableName: config('permission.table_names.model_has_roles', 'model_has_roles'),
                pivotColumn: 'role_id',
                fromModelType: self::CurrentUserModelType,
                toModelType: self::LegacyUserModelType,
            );

            $this->backfillModelType(
                tableName: config('permission.table_names.model_has_permissions', 'model_has_permissions'),
                pivotColumn: 'permission_id',
                fromModelType: self::CurrentUserModelType,
                toModelType: self::LegacyUserModelType,
            );
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function backfillModelType(
        string $tableName,
        string $pivotColumn,
        string $fromModelType,
        string $toModelType,
    ): void {
        $legacyAssignments = DB::table($tableName)
            ->select([$pivotColumn, 'model_id'])
            ->where('model_type', $fromModelType)
            ->get();

        foreach ($legacyAssignments as $assignment) {
            $legacyRow = [
                $pivotColumn => $assignment->{$pivotColumn},
                'model_id' => $assignment->model_id,
                'model_type' => $fromModelType,
            ];

            $currentRow = [
                $pivotColumn => $assignment->{$pivotColumn},
                'model_id' => $assignment->model_id,
                'model_type' => $toModelType,
            ];

            if (DB::table($tableName)->where($currentRow)->exists()) {
                DB::table($tableName)->where($legacyRow)->delete();

                continue;
            }

            DB::table($tableName)->where($legacyRow)->update([
                'model_type' => $toModelType,
            ]);
        }
    }
};
