<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogData;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;

final class DeleteUser
{
    public static function handle(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $before = self::auditState($user);
            $user->delete();

            DB::afterCommit(fn (): AuditLog => RecordAuditLog::handle(new AuditLogData(
                event: 'users.deleted',
                summary: sprintf('Deleted user %s.', $user->email),
                subjectType: User::class,
                subjectId: (int) $user->getKey(),
                subjectLabel: $user->email,
                changes: ['before' => $before, 'after' => null],
            )));
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
