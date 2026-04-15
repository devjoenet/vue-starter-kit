<?php

declare(strict_types=1);

namespace Modules\Audit\DTOs;

use Modules\Audit\Http\Requests\IndexAuditLogsRequest;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class AuditLogIndexQueryData extends Data
{
    /**
     * @param  list<string>  $actors
     * @param  list<string>  $events
     * @param  list<string>  $subject_types
     */
    public function __construct(
        public string $sort,
        public string $direction,
        public array $actors,
        public array $events,
        public array $subject_types,
        public ?string $search = null,
        public ?string $from = null,
        public ?string $until = null,
    ) {}

    public static function fromRequest(IndexAuditLogsRequest $request): self
    {
        return new self(
            sort: (string) $request->validated('sort', 'created_at'),
            direction: (string) $request->validated('direction', 'desc'),
            actors: self::normalizeList($request->validated('actors', [])),
            events: self::normalizeList($request->validated('events', [])),
            subject_types: self::normalizeList($request->validated('subject_types', [])),
            search: self::normalizeString($request->validated('search')),
            from: self::normalizeString($request->validated('from')),
            until: self::normalizeString($request->validated('until')),
        );
    }

    private static function normalizeString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $normalizedValue = mb_trim($value);

        return $normalizedValue === '' ? null : $normalizedValue;
    }

    private static function normalizeList(mixed $values): array
    {
        if (! is_array($values)) {
            return [];
        }

        return collect($values)
            ->filter(fn (mixed $value): bool => is_scalar($value) && mb_trim((string) $value) !== '')
            ->map(fn (mixed $value): string => (string) $value)
            ->unique()
            ->values()
            ->all();
    }
}
