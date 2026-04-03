<?php

declare(strict_types=1);

namespace App\Modules\Settings\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdatePassword
{
    public static function handle(User $user, string $password): User
    {
        return DB::transaction(function () use ($user, $password): User {
            $user->update(['password' => $password]);

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: 'settings.password_updated',
                summary: sprintf('Updated password for %s.', $user->email),
                subjectType: User::class,
                subjectId: (int) $user->getKey(),
                subjectLabel: $user->email,
                context: ['password_updated' => true],
            )));

            return $user;
        });
    }
}
