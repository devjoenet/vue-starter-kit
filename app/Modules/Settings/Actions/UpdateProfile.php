<?php

declare(strict_types=1);

namespace App\Modules\Settings\Actions;

use App\Modules\Settings\DTOs\UpdateProfileData;
use App\Modules\Settings\Events\ProfileUpdated;
use App\Modules\Shared\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateProfile
{
    public static function handle(User $user, UpdateProfileData $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $before = self::auditState($user);
            $user->fill($data->all());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            event(new ProfileUpdated($user, $before));

            return $user;
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
