<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Contracts;

use App\Modules\IAM\Roles\DTOs\RoleIndexFilterOptionsData;

interface RoleFilterOptionsProvider
{
    public function options(): RoleIndexFilterOptionsData;
}
