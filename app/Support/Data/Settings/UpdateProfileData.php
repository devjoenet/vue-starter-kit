<?php

declare(strict_types=1);

namespace App\Support\Data\Settings;

use Spatie\LaravelData\Data;

final class UpdateProfileData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}
}
