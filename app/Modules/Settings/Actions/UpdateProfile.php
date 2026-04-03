<?php

declare(strict_types=1);

namespace App\Modules\Settings\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Settings\DTOs\UpdateProfileData;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateProfile
{
    public static function handle(User $user, UpdateProfileData $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $before = self::auditState($user);
            $user->fill($data->all());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: 'settings.profile_updated',
                summary: sprintf('Updated profile for %s.', $user->email),
                subjectType: User::class,
                subjectId: (int) $user->getKey(),
                subjectLabel: $user->email,
                changes: ['before' => $before, 'after' => self::auditState($user)],
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
