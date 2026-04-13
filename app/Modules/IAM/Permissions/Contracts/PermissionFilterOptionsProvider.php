<?php

declare(strict_types=1);

namespace App\Modules\IAM\Permissions\Contracts;

use App\Modules\IAM\Permissions\DTOs\PermissionIndexFilterOptionsData;

interface PermissionFilterOptionsProvider
{
    public function options(): PermissionIndexFilterOptionsData;
}
