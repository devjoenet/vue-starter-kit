<?php

declare(strict_types=1);

namespace App\Support\Data\Admin\Permissions;

use Spatie\LaravelData\Data;

final class UpdatePermissionData extends Data
{
    public function __construct(
        public string $name,
        public string $group,
    ) {}
}
