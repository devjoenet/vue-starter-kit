<?php

declare(strict_types=1);

namespace Modules\Auth\Actions;

use Illuminate\Support\Facades\Validator;
use Modules\Core\Actions\PasswordValidationRules;
use Modules\Core\Actions\UserIdentityValidationRules;
use Modules\Users\DTOs\CreateUserData;

final class ValidateRegistrationInput
{
    /** @param  array<string, mixed>  $input */
    public static function handle(array $input): CreateUserData
    {
        /** @var array{name: string, email: string, password: string} $validated */
        $validated = Validator::make($input, [
            'name' => UserIdentityValidationRules::name(),
            'email' => UserIdentityValidationRules::email(),
            'password' => PasswordValidationRules::password(),
        ])->validate();

        return CreateUserData::fromInput($validated);
    }
}
