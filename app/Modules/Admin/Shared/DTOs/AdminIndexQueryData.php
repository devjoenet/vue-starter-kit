<?php

declare(strict_types=1);

namespace App\Modules\Admin\Shared\DTOs;

use App\Modules\Admin\Shared\Support\AdminIndexQuery;
use Spatie\LaravelData\Data;

final class AdminIndexQueryData extends Data
{
    /**
     * @param  array<string, list<string>>  $filters
     */
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
