<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Users\DTOs\UpdateUserData;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class UpdateUser
{
    public static function handle(User $user, UpdateUserData $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $before = self::auditState($user);
            $passwordUpdated = $data->passwordWasProvided();

            $user->forceFill(['name' => $data->name, 'email' => $data->email]);

            if ($passwordUpdated) {
                $user->forceFill(['password' => Hash::make($data->password)]);
            }

            $user->save();

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: 'users.updated',
                summary: sprintf('Updated user %s.', $user->email),
                subjectType: User::class,
                subjectId: (int) $user->getKey(),
                subjectLabel: $user->email,
                changes: ['before' => $before, 'after' => self::auditState($user)],
                context: ['password_updated' => $passwordUpdated],
            )));

            return $user;
        });
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
