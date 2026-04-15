<?php

declare(strict_types=1);

namespace Modules\Permissions\DTOs;

use Modules\Permissions\Models\Permission;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class PermissionIndexItemData extends Data
{
    public function __construct(
        public int $id,
        public string $group,
        public string $group_label,
        public ?string $group_description,
        public string $name,
        public string $label,
        public ?string $description,
        public string $suffix,
    ) {}

    public static function fromModel(Permission $permission): self
    {
        return new self(
            id: (int) $permission->id,
            group: $permission->group,
            group_label: $permission->group_label,
            group_description: $permission->group_description,
            name: $permission->name,
            label: $permission->display_label,
            description: $permission->description,
            suffix: $permission->action,
        );
    }
}
