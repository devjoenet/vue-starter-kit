<?php

declare(strict_types=1);

namespace App\Modules\Admin\Roles\DTOs;

use App\Models\Role;
use Spatie\LaravelData\Data;

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
