<?php

declare(strict_types=1);

namespace Modules\Auth\Actions;

use Laravel\Fortify\Contracts\ResetsUserPasswords;
use Modules\Core\Models\User;

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
