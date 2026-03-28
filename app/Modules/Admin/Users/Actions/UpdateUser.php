<?php

declare(strict_types=1);

namespace App\Modules\Admin\Users\Actions;

use App\Models\User;
use App\Modules\Admin\Users\DTOs\UpdateUserData;
use Illuminate\Support\Facades\Hash;

final class UpdateUser
{
    public static function handle(User $user, UpdateUserData $data): User
    {
        $user->forceFill([
            'name' => $data->name,
            'email' => $data->email,
        ]);

        if ($data->password !== null) {
            $user->forceFill([
                'password' => Hash::make($data->password),
            ]);
        }

        $user->save();

        return $user;
    }
}
