<?php

declare(strict_types=1);

namespace App\Modules\IAM\DTOs;

use App\Modules\IAM\Models\Role;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class RoleListItemData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public int $users_count,
    ) {}

    public static function fromModel(Role $role): self
    {
        return new self(
            id: (int) $role->id,
            name: $role->name,
            users_count: (int) $role->users_count,
        );
    }
}
