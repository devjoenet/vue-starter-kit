<?php

declare(strict_types=1);

namespace App\Modules\Roles\DTOs;

use Spatie\LaravelData\Data;

final class CreateRoleData extends Data
{
    /** @param  list<int>  $user_ids */
    public function __construct(
        public string $name,
        public array $user_ids = [],
    ) {}
}
