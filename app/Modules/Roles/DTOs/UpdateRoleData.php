<?php

declare(strict_types=1);

namespace App\Modules\Roles\DTOs;

use Spatie\LaravelData\Data;

final class UpdateRoleData extends Data
{
    public function __construct(
        public string $name,
    ) {}
}
