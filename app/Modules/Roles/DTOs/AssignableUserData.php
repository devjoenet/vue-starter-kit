<?php

declare(strict_types=1);

namespace App\Modules\Roles\DTOs;

use App\Modules\Users\Models\User;
use Spatie\LaravelData\Data;

final class AssignableUserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );
    }
}
