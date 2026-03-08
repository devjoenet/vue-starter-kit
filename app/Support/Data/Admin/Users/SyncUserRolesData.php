<?php

declare(strict_types=1);

namespace App\Support\Data\Admin\Users;

use Spatie\LaravelData\Data;

final class SyncUserRolesData extends Data
{
    /**
     * @param  list<string>  $roles
     */
    public function __construct(
        public array $roles,
    ) {}
}
