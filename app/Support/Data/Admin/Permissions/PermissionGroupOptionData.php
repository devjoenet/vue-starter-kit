<?php

declare(strict_types=1);

namespace App\Support\Data\Admin\Permissions;

use App\Models\PermissionGroup;
use Spatie\LaravelData\Data;

final class PermissionGroupOptionData extends Data
{
    public function __construct(
        public string $slug,
        public string $label,
        public ?string $description,
        public int $permissions_count,
    ) {}

    public static function fromModel(PermissionGroup $group): self
    {
        return new self(
            slug: $group->slug,
            label: $group->label,
            description: $group->description,
            permissions_count: (int) $group->permissions_count,
        );
    }
}
