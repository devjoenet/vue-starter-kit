<?php

declare(strict_types=1);

namespace Modules\Permissions\Contracts;

use Modules\Permissions\DTOs\PermissionIndexFilterOptionsData;

interface PermissionFilterOptionsProvider
{
    public function options(): PermissionIndexFilterOptionsData;
}
