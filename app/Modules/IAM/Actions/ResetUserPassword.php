<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\Shared\Actions\PasswordValidationRules;
use App\Modules\Shared\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

final class ResetUserPassword implements ResetsUserPasswords
{
    /** @param  array<string, string>  $input */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => PasswordValidationRules::password(),
        ])->validate();

        $user->forceFill([
            'password' => $input['password'],
        ])->save();
    }
}
