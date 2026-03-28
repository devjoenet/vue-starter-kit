<?php

declare(strict_types=1);

namespace App\Actions\Settings;

use App\Models\User;
use App\Support\Data\Settings\UpdateProfileData;

final class UpdateProfile
{
    public static function handle(User $user, UpdateProfileData $data): User
    {
        $user->fill($data->all());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }
}
