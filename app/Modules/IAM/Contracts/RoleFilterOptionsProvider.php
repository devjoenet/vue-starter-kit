<?php

declare(strict_types=1);

namespace App\Modules\IAM\Contracts;

use App\Modules\IAM\DTOs\RoleIndexFilterOptionsData;

interface RoleFilterOptionsProvider
{
    public function options(): RoleIndexFilterOptionsData;
}
