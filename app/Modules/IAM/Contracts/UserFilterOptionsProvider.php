<?php

declare(strict_types=1);

namespace App\Modules\IAM\Contracts;

use App\Modules\IAM\DTOs\UserIndexFilterOptionsData;

interface UserFilterOptionsProvider
{
    public function options(): UserIndexFilterOptionsData;
}
