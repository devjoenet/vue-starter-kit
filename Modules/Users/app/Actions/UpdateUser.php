<?php

declare(strict_types=1);

namespace Modules\Users\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\User;
use Modules\Users\DTOs\UpdateUserData;
use Modules\Users\Events\UserUpdated;

final class UpdateUser
{
    public static function handle(User $user, UpdateUserData $data): void
    {
        DB::transaction(function () use ($user, $data): void {
            $before = self::auditState($user);
            $passwordUpdated = $data->passwordWasProvided();

            $user->forceFill(['name' => $data->name, 'email' => $data->email]);

            if ($passwordUpdated) {
                $user->forceFill(['password' => Hash::make($data->password)]);
            }

            $user->save();

            event(new UserUpdated($user, $before, $passwordUpdated));
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
