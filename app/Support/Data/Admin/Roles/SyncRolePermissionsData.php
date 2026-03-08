<?php

declare(strict_types=1);

namespace App\Support\Data\Admin\Roles;

use Spatie\LaravelData\Data;

final class SyncRolePermissionsData extends Data
{
    /**
     * @param  list<string>  $permissions
     */
    public function __construct(
        public array $permissions,
    ) {}
}
