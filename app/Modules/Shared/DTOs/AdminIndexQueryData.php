<?php

declare(strict_types=1);

namespace App\Modules\Shared\DTOs;

use App\Modules\Shared\Actions\AdminIndexQuery;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class AdminIndexQueryData extends Data
{
    /** @param  array<string, list<string>>  $filters */
    public function __construct(
        public string $sort,
        public string $direction,
        public array $filters,
    ) {}

    public static function fromQuery(AdminIndexQuery $query): self
    {
        return new self(
            sort: $query->sort,
            direction: $query->direction,
            filters: $query->filters,
        );
    }
}
