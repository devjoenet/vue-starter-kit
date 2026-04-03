<?php

declare(strict_types=1);

namespace App\Modules\Users\DTOs;

use Spatie\LaravelData\Data;

final class CreateUserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    /** @param  array{name: string, email: string, password: string}  $input */
    public static function fromInput(array $input): self
    {
        return new self(
            name: $input['name'],
            email: $input['email'],
            password: $input['password'],
        );
    }
}
