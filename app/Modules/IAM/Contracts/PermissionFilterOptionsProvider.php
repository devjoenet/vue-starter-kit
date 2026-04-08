<?php

declare(strict_types=1);

namespace App\Modules\IAM\Contracts;

use App\Modules\IAM\DTOs\PermissionIndexFilterOptionsData;

interface PermissionFilterOptionsProvider
{
    public function options(): PermissionIndexFilterOptionsData;
}
