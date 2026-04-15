<?php

declare(strict_types=1);

namespace Modules\Roles\DTOs;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class RoleIndexFilterOptionsData extends Data
{
    /**
     * @param  list<string>  $display_name
     * @param  list<string>  $slug
     * @param  list<string>  $users
     */
    public function __construct(
        public array $display_name,
        public array $slug,
        public array $users,
    ) {}
}
