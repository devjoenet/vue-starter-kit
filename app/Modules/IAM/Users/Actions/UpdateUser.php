<?php

declare(strict_types=1);

namespace App\Modules\IAM\Users\Actions;

use App\Modules\IAM\Users\DTOs\UpdateUserData;
use App\Modules\IAM\Users\Events\UserUpdated;
use App\Modules\Shared\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
