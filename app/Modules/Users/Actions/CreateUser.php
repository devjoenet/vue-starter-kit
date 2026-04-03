<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Users\DTOs\CreateUserData;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class CreateUser
{
    public static function handle(CreateUserData $data): User
    {
        return DB::transaction(function () use ($data): User {
            $existingUser = User::withTrashed()->where('email', $data->email)->first();
            $before = $existingUser instanceof User ? self::auditState($existingUser) : null;
            $user = self::restoreOrCreateUser($data);

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: $existingUser?->trashed() ? 'users.restored' : 'users.created',
                summary: ($existingUser?->trashed() ? 'Restored' : 'Created').sprintf(' user %s.', $user->email),
                subjectType: User::class,
                subjectId: (int) $user->getKey(),
                subjectLabel: $user->email,
                changes: ['before' => $before, 'after' => self::auditState($user)],
            )));

            return $user;
        });
    }

    private static function restoreOrCreateUser(CreateUserData $data): User
    {
        $user = User::withTrashed()->firstOrNew([
            'email' => $data->email,
        ]);

        $user->forceFill([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
            'email_verified_at' => null,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'remember_token' => null,
            'deleted_at' => null,
        ])->save();

        return $user;
    }

    /** @return array{name: string, email: string} */
    private static function auditState(User $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
