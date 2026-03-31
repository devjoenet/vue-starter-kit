<?php

declare(strict_types=1);

namespace App\Modules\Shared\Actions;

use Illuminate\Http\Request;

final readonly class AdminIndexQuery
{
    /** @param  array<string, list<string>>  $filters */
    public function __construct(
        public string $sort,
        public string $direction,
        public array $filters,
    ) {}

    /**
     * @param  list<string>  $allowedSorts
     * @param  list<string>  $allowedFilters
     */
    public static function fromRequest(
        Request $request,
        array $allowedSorts,
        array $allowedFilters,
        string $defaultSort = 'id',
    ): self {
        $sort = (string) $request->query('sort', $defaultSort);

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = $defaultSort;
        }

        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';
        $rawFilters = $request->query('filters', []);
        $filters = [];

        if (is_array($rawFilters)) {
            foreach ($allowedFilters as $filterKey) {
                $filterValues = $rawFilters[$filterKey] ?? [];

                if (! is_array($filterValues)) {
                    $filterValues = [$filterValues];
                }

                $normalizedValues = collect($filterValues)
                    ->filter(fn (mixed $value): bool => is_scalar($value) && mb_trim((string) $value) !== '')
                    ->map(fn (mixed $value): string => (string) $value)
                    ->unique()
                    ->values()
                    ->all();

                if ($normalizedValues === []) {
                    continue;
                }

                $filters[$filterKey] = $normalizedValues;
            }
        }

        return new self(
            sort: $sort,
            direction: $direction,
            filters: $filters,
        );
    }

    /** @return list<string> */
    public function filterValues(string $key): array
    {
        return $this->filters[$key] ?? [];
    }
}
