<?php

declare(strict_types=1);

namespace App\Modules\Admin\Permissions\DTOs;

use Spatie\LaravelData\Data;

final class UpdatePermissionData extends Data
{
    public function __construct(
        public string $label,
        public ?string $description,
        public string $group,
        public string $groupLabel,
        public ?string $groupDescription,
    ) {}
}
