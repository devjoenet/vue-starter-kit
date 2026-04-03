<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Shared\Actions\PasswordValidationRules;
use App\Modules\Users\DTOs\CreateUserData;
use App\Modules\Users\Models\User;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

final class CreateNewUser implements CreatesNewUsers
{
    /** @param  array<string, string>  $input */
    public function create(array $input): User
    {
        /** @var array{name: string, email: string, password: string} $validated */
        $validated = Validator::make($input, [
            'name' => ProfileValidationRules::name(),
            'email' => $this->registrationEmailRules(),
            'password' => PasswordValidationRules::password(),
        ])->validate();

        return CreateUser::handle(CreateUserData::fromInput($validated));
    }

    /** @return array<int, mixed> */
    private function registrationEmailRules(): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users', 'email')->where(
                fn (QueryBuilder $query): QueryBuilder => $query->whereNull('deleted_at'),
            ),
        ];
    }
}
