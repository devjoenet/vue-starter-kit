<?php

declare(strict_types=1);

namespace App\Support\Data\Admin\Users;

use Spatie\LaravelData\Data;

final class CreateUserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}
