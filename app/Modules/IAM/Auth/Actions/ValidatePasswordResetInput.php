<?php

declare(strict_types=1);

namespace App\Modules\IAM\Auth\Actions;

use App\Modules\Shared\Actions\PasswordValidationRules;
use Illuminate\Support\Facades\Validator;

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
