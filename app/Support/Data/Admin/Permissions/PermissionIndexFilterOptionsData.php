<?php

declare(strict_types=1);

namespace App\Support\Data\Admin\Permissions;

use Spatie\LaravelData\Data;

final class PermissionIndexFilterOptionsData extends Data
{
    /**
     * @param  list<string>  $group
     * @param  list<string>  $permission
     * @param  list<string>  $permission_check
     */
    public function __construct(
        public array $group,
        public array $permission,
        public array $permission_check,
    ) {}
}
