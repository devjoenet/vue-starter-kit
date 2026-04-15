<?php

declare(strict_types=1);

namespace Modules\Auth\Actions;

use Illuminate\Support\Facades\Validator;
use Modules\Core\Actions\PasswordValidationRules;

final class ValidatePasswordResetInput
{
    /** @param  array<string, mixed>  $input */
    public static function handle(array $input): string
    {
        /** @var array{password: string} $validated */
        $validated = Validator::make($input, [
            'password' => PasswordValidationRules::password(),
        ])->validate();

        return $validated['password'];
    }
}
