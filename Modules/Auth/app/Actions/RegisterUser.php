<?php

declare(strict_types=1);

namespace Modules\Auth\Actions;

use Laravel\Fortify\Contracts\CreatesNewUsers;
use Modules\Core\Models\User;
use Modules\Users\Actions\CreateUser;

final class RegisterUser implements CreatesNewUsers
{
    /** @param  array<string, string>  $input */
    public function create(array $input): User
    {
        return CreateUser::handle(ValidateRegistrationInput::handle($input));
    }
}
