<?php

declare(strict_types=1);

namespace App\Modules\Roles\DTOs;

use Spatie\LaravelData\Data;

final class SyncRolePermissionsData extends Data
{
    /** @param  list<string>  $permissions */
    public function __construct(
        public array $permissions,
    ) {}
}
