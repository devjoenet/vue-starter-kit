<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Users\DTOs\CreateUserData;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Hash;

final class CreateUser
{
    public static function handle(CreateUserData $data): User
    {
        $user = User::withTrashed()->firstOrNew([
            'email' => $data->email,
        ]);

        $user->forceFill([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
            'deleted_at' => null,
        ])->save();

        return $user;
    }
}
