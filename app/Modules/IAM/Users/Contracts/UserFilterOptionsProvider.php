<?php

declare(strict_types=1);

namespace App\Modules\IAM\Users\Contracts;

use App\Modules\IAM\Users\DTOs\UserIndexFilterOptionsData;

interface UserFilterOptionsProvider
{
    public function options(): UserIndexFilterOptionsData;
}
