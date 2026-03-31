<?php

declare(strict_types=1);

namespace App\Modules\Users\Contracts;

use App\Modules\Users\DTOs\UserIndexFilterOptionsData;

interface UserFilterOptionsProvider
{
    public function options(): UserIndexFilterOptionsData;
}
