<?php

declare(strict_types=1);

namespace App\Support\Data\Admin\Roles;

use Spatie\LaravelData\Data;

final class CreateRoleData extends Data
{
    /**
     * @param  list<int>  $user_ids
     */
    public function __construct(
        public string $name,
        public array $user_ids = [],
    ) {}
}
