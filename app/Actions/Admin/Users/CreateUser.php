<?php

declare(strict_types=1);

namespace App\Actions\Admin\Users;

use App\Models\User;
use App\Support\Data\Admin\Users\CreateUserData;
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
