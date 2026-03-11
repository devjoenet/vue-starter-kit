<?php

declare(strict_types=1);

namespace App\Support\Data\Admin\Users;

use Spatie\LaravelData\Data;

final class UserIndexFilterOptionsData extends Data
{
    /**
     * @param  list<string>  $name
     * @param  list<string>  $email
     * @param  list<string>  $roles
     */
    public function __construct(
        public array $name,
        public array $email,
        public array $roles,
    ) {}
}
