<?php

declare(strict_types=1);

namespace App\Support\Data\Admin\Permissions;

use App\Models\Permission;
use Spatie\LaravelData\Data;

final class PermissionItemData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $group,
    ) {}

    public static function fromModel(Permission $permission): self
    {
        return new self(
            id: (int) $permission->id,
            name: $permission->name,
            group: $permission->group,
        );
    }
}
