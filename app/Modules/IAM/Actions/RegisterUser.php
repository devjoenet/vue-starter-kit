<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use App\Modules\IAM\DTOs\CreateUserData;
use App\Modules\Shared\Actions\PasswordValidationRules;
use App\Modules\Shared\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

final class RegisterUser implements CreatesNewUsers
{
    /** @param  array<string, string>  $input */
    public function create(array $input): User
    {
        /** @var array{name: string, email: string, password: string} $validated */
        $validated = Validator::make($input, [
            'name' => ProfileValidationRules::name(),
            'email' => ProfileValidationRules::email(),
            'password' => PasswordValidationRules::password(),
        ])->validate();

        return CreateUser::handle(CreateUserData::fromInput($validated));
    }
}
