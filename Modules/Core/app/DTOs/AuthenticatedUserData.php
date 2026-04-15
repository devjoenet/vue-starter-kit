<?php

declare(strict_types=1);

namespace Modules\Core\DTOs;

use Carbon\CarbonInterface;
use Modules\Core\Models\User;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
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
        $emailVerifiedAt = $user->email_verified_at;

        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            email_verified_at: $emailVerifiedAt instanceof CarbonInterface
                ? $emailVerifiedAt->toJSON()
                : null,
        );
    }
}
