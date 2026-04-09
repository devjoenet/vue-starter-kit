<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\Shared\Models\User;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

final class ResetUserPassword implements ResetsUserPasswords
{
    /** @param  array<string, string>  $input */
    public function reset(User $user, array $input): void
    {
        $user->forceFill([
            'password' => ValidatePasswordResetInput::handle($input),
        ])->save();
    }
}
