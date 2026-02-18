<?php

declare(strict_types=1);

namespace App\Data\Admin;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

final class UserUpsertData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,

        #[Email, Max(255)]
        public string $email,

        #[Min(8), Confirmed]
        public ?string $password,
    ) {}
}
