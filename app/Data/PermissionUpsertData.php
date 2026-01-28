<?php

declare(strict_types=1);

namespace App\Data\Admin;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class PermissionUpsertData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,

        #[In(['users', 'roles', 'permissions'])]
        public string $group,
    ) {}
}
