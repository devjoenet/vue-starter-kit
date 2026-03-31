<?php

declare(strict_types=1);

namespace App\Modules\Users\DTOs;

use Spatie\LaravelData\Data;

final class UpdateUserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
    ) {}
}
