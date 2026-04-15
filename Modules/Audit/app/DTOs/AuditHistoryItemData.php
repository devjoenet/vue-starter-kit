<?php

declare(strict_types=1);

namespace Modules\Audit\DTOs;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Audit\Models\AuditLog;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class AuditHistoryItemData extends Data
{
    private const string REDACTED_VALUE = 'Redacted';

    /** @var list<string> */
    private const array SENSITIVE_FIELD_MARKERS = [
        'api_key',
        'credential',
        'current_password',
        'new_password',
        'password',
        'recovery_code',
        'remember_token',
        'secret',
        'token',
        'two_factor',
    ];

    /** @param  Collection<int, AuditHistoryChangeData>  $changes */
    public function __construct(
        public int $id,
        public string $created_at,
        public string $event,
        public string $summary,
        public ?string $actor_label,
        public Collection $changes,
    ) {}

    public static function fromModel(AuditLog $auditLog): self
    {
        $createdAt = $auditLog->created_at;
        assert($createdAt instanceof CarbonInterface);

        return new self(
            id: $auditLog->id,
            created_at: $createdAt->toJSON(),
            event: $auditLog->event,
            summary: $auditLog->summary,
            actor_label: $auditLog->actor_label,
            changes: self::changesFromAuditLog($auditLog),
        );
    }

    /** @return Collection<int, AuditHistoryChangeData> */
    private static function changesFromAuditLog(AuditLog $auditLog): Collection
    {
        $changes = $auditLog->changes;

        if (! is_array($changes)) {
            return collect();
        }

        $before = self::stateFromChanges($changes, 'before');
        $after = self::stateFromChanges($changes, 'after');

        return collect([...array_keys($before), ...array_keys($after)])
            ->unique()
            ->sort()
            ->values()
            ->map(fn (string $field): AuditHistoryChangeData => new AuditHistoryChangeData(
                field: $field,
                before: self::formatFieldValue($field, $before[$field] ?? null),
                after: self::formatFieldValue($field, $after[$field] ?? null),
            ));
    }

    /**
     * @param  array<string, mixed>  $changes
     * @return array<string, mixed>
     */
    private static function stateFromChanges(array $changes, string $key): array
    {
        $state = $changes[$key] ?? null;

        return is_array($state) ? $state : [];
    }

    private static function formatFieldValue(string $field, mixed $value): ?string
    {
        if (self::fieldIsSensitive($field)) {
            return self::REDACTED_VALUE;
        }

        return self::formatValue($value);
    }

    private static function fieldIsSensitive(string $field): bool
    {
        $normalizedField = Str::of($field)->snake()->lower()->toString();

        return Str::contains($normalizedField, self::SENSITIVE_FIELD_MARKERS);
    }

    private static function formatValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            return self::formatArrayValue($value);
        }

        return null;
    }

    /** @param  array<mixed>  $value */
    private static function formatArrayValue(array $value): string
    {
        if ($value === []) {
            return 'Empty';
        }

        if (array_is_list($value) && collect($value)->every(fn (mixed $entry): bool => is_scalar($entry) || $entry === null)) {
            return collect($value)
                ->map(fn (mixed $entry): string => $entry === null ? 'None' : (string) $entry)
                ->implode(', ');
        }

        $encodedValue = json_encode($value);

        return is_string($encodedValue) ? $encodedValue : 'Complex value';
    }
}
