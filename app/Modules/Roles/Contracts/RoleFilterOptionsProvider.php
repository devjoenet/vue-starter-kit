<?php

declare(strict_types=1);

namespace App\Modules\Roles\Contracts;

use App\Modules\Roles\DTOs\RoleIndexFilterOptionsData;

interface RoleFilterOptionsProvider
{
    public function options(): RoleIndexFilterOptionsData;
}
