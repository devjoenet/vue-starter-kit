<?php

declare(strict_types=1);

namespace App\Modules\Permissions\DTOs;

use Spatie\LaravelData\Data;

final class CreatePermissionData extends Data
{
    public function __construct(
        public string $name,
        public string $label,
        public ?string $description,
        public string $group,
        public string $groupLabel,
        public ?string $groupDescription,
    ) {}
}
