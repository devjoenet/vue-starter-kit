<?php

declare(strict_types=1);

namespace App\Modules\Users\DTOs;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
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
