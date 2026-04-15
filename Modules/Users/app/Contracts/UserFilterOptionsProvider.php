<?php

declare(strict_types=1);

namespace Modules\Users\Contracts;

use Modules\Users\DTOs\UserIndexFilterOptionsData;

interface UserFilterOptionsProvider
{
    public function options(): UserIndexFilterOptionsData;
}
