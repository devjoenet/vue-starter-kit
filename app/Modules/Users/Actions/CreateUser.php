<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Users\DTOs\CreateUserData;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class CreateUser
{
    public static function handle(CreateUserData $data): User
    {
        return DB::transaction(fn (): User => self::restoreOrCreateUser($data));
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
}
