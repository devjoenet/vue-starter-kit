<?php

declare(strict_types=1);

namespace App\Actions\Admin\Users;

use App\Models\User;
use App\Support\Data\Admin\Users\CreateUserData;
use Illuminate\Support\Facades\Hash;

final class CreateUser
{
    public function handle(CreateUserData $data): User
    {
        return User::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);
    }
}
