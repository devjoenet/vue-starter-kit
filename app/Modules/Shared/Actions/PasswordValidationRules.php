<?php

declare(strict_types=1);

namespace App\Modules\Shared\Actions;

use Illuminate\Validation\Rules\Password;

final class PasswordValidationRules
{
    /** @return array<int, string> */
    public static function currentPassword(): array
    {
        return ['required', 'string', 'current_password'];
    }

    /** @return array<int, Password|string> */
    public static function password(): array
    {
        return ['required', 'string', Password::default(), 'confirmed'];
    }
}
