<?php

declare(strict_types=1);

namespace App\Support\Data\Admin\Users;

use App\Models\User;
use Spatie\LaravelData\Data;

final class EditableUserData extends Data
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
