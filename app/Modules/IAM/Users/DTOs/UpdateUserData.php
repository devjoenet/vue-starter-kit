<?php

declare(strict_types=1);

namespace App\Modules\IAM\Users\DTOs;

use Spatie\LaravelData\Data;

final class UpdateUserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
    ) {}

    /** @param  array{name: string, email: string, password?: string|null}  $input */
    public static function fromInput(array $input): self
    {
        return new self(
            name: $input['name'],
            email: $input['email'],
            password: $input['password'] ?? null,
        );
    }

    public function passwordWasProvided(): bool
    {
        return $this->password !== null;
    }
}
