<?php

declare(strict_types=1);

namespace Modules\Roles\Contracts;

use Modules\Roles\DTOs\RoleIndexFilterOptionsData;

interface RoleFilterOptionsProvider
{
    public function options(): RoleIndexFilterOptionsData;
}
