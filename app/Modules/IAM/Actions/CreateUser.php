<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\DTOs\CreateUserData;
use App\Modules\IAM\Events\UserCreated;
use App\Modules\Shared\Models\User;
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

            event(new UserCreated($user, $before, $existingUser?->trashed() === true));

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
