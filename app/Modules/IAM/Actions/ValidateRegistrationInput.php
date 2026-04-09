<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\DTOs\CreateUserData;
use App\Modules\Shared\Actions\PasswordValidationRules;
use Illuminate\Support\Facades\Validator;

final class ValidateRegistrationInput
{
    /** @param  array<string, mixed>  $input */
    public static function handle(array $input): CreateUserData
    {
        /** @var array{name: string, email: string, password: string} $validated */
        $validated = Validator::make($input, [
            'name' => ProfileValidationRules::name(),
            'email' => ProfileValidationRules::email(),
            'password' => PasswordValidationRules::password(),
        ])->validate();

        return CreateUserData::fromInput($validated);
    }
}
