<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Shared\Actions\PasswordValidationRules;
use App\Modules\Users\DTOs\CreateUserData;
use App\Modules\Users\Models\User;
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
