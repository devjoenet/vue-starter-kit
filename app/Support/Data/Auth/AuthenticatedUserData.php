<?php

declare(strict_types=1);

namespace App\Support\Data\Auth;

use App\Models\User;
use Spatie\LaravelData\Data;

final class AuthenticatedUserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $email_verified_at,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            email_verified_at: $user->email_verified_at?->toJSON(),
        );
    }
}
