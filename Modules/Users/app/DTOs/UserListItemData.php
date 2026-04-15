<?php

declare(strict_types=1);

namespace Modules\Users\DTOs;

use Modules\Core\Models\User;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class UserListItemData extends Data
{
    /** @param  list<string>  $roles */
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public array $roles,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            roles: $user->roles->pluck('name')->values()->all(),
        );
    }
}
