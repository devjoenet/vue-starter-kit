<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Contracts;

use App\Modules\Permissions\DTOs\PermissionIndexFilterOptionsData;

interface PermissionFilterOptionsProvider
{
    public function options(): PermissionIndexFilterOptionsData;
}
