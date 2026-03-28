<?php

declare(strict_types=1);

namespace App\Modules\Admin\Users\DTOs;

use App\Models\Role;
use Spatie\LaravelData\Data;

final class RoleOptionData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}

    public static function fromModel(Role $role): self
    {
        return new self(
            id: (int) $role->id,
            name: $role->name,
        );
    }
}
