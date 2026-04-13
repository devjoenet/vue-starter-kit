<?php

declare(strict_types=1);

namespace App\Modules\IAM\Auth\Actions;

use App\Modules\IAM\Users\Actions\CreateUser;
use App\Modules\Shared\Models\User;
use Laravel\Fortify\Contracts\CreatesNewUsers;

final class RegisterUser implements CreatesNewUsers
{
    /** @param  array<string, string>  $input */
    public function create(array $input): User
    {
        return CreateUser::handle(ValidateRegistrationInput::handle($input));
    }
}
