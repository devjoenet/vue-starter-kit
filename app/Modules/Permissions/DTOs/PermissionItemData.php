<?php

declare(strict_types=1);

namespace App\Modules\Permissions\DTOs;

use App\Modules\Permissions\Models\Permission;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class PermissionItemData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $label,
        public ?string $description,
        public string $group,
        public string $group_label,
        public ?string $group_description,
    ) {}

    public static function fromModel(Permission $permission): self
    {
        return new self(
            id: (int) $permission->id,
            name: $permission->name,
            label: $permission->display_label,
            description: $permission->description,
            group: $permission->group,
            group_label: $permission->group_label,
            group_description: $permission->group_description,
        );
    }
}
