<?php

declare(strict_types=1);

namespace App\Data\Admin;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class PermissionUpsertData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,

        #[Max(255)]
        public string $group,
    ) {}
}
