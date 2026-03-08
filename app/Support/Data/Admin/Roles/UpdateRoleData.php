<?php

declare(strict_types=1);

namespace App\Support\Data\Admin\Roles;

use Spatie\LaravelData\Data;

final class UpdateRoleData extends Data
{
    public function __construct(
        public string $name,
    ) {}
}
