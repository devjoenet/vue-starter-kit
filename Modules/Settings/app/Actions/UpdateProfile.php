<?php

declare(strict_types=1);

namespace Modules\Settings\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Settings\DTOs\UpdateProfileData;
use Modules\Settings\Events\ProfileUpdated;

final class UpdateProfile
{
    public static function handle(User $user, UpdateProfileData $data): void
    {
        DB::transaction(function () use ($user, $data): void {
            $before = self::auditState($user);
            $user->fill($data->all());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            event(new ProfileUpdated($user, $before));
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
