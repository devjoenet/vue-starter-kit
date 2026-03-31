<?php

declare(strict_types=1);

namespace App\Modules\Users\DTOs;

use Spatie\LaravelData\Data;

final class SyncUserRolesData extends Data
{
    /** @param  list<string>  $roles */
    public function __construct(
        public array $roles,
    ) {}
}
