<?php

declare(strict_types=1);

namespace App\Support\Data\Admin\Permissions;

use App\Models\Permission;
use Spatie\LaravelData\Data;

final class PermissionIndexItemData extends Data
{
    public function __construct(
        public int $id,
        public string $group,
        public string $name,
        public string $suffix,
    ) {}

    public static function fromModel(Permission $permission): self
    {
        $segments = array_values(array_filter(explode('.', $permission->name)));

        return new self(
            id: (int) $permission->id,
            group: $permission->group,
            name: $permission->name,
            suffix: (string) (end($segments) ?: $permission->name),
        );
    }
}
