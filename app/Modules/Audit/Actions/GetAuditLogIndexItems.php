<?php

declare(strict_types=1);

namespace App\Modules\Audit\Actions;

use App\Modules\Audit\DTOs\AuditLogIndexItemData;
use App\Modules\Audit\DTOs\AuditLogIndexQueryData;
use App\Modules\Audit\Models\AuditLog;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetAuditLogIndexItems
{
    /** @return LengthAwarePaginator<int, AuditLogIndexItemData> */
    public static function handle(AuditLogIndexQueryData $query): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, AuditLogIndexItemData> $auditLogs */
        $auditLogs = AuditLogIndexItemData::collect(
            self::query($query)
                ->paginate(20)
                ->withQueryString(),
        );

        return $auditLogs;
    }

    /** @return Builder<AuditLog> */
    private static function query(AuditLogIndexQueryData $query): Builder
    {
        $builder = AuditLog::query()->select([
            'audit_logs.id',
            'audit_logs.created_at',
            'audit_logs.event',
            'audit_logs.summary',
            'audit_logs.actor_label',
            'audit_logs.subject_type',
            'audit_logs.subject_id',
            'audit_logs.subject_label',
            'audit_logs.method',
            'audit_logs.url',
            'audit_logs.ip_address',
        ]);

        self::applyFilters($builder, $query);
        self::applySorting($builder, $query);

        return $builder;
    }

    /** @param  Builder<AuditLog>  $builder */
    private static function applyFilters(Builder $builder, AuditLogIndexQueryData $query): void
    {
        if ($query->actors !== []) {
            $builder->whereIn('audit_logs.actor_label', $query->actors);
        }

        if ($query->events !== []) {
            $builder->whereIn('audit_logs.event', $query->events);
        }

        if ($query->subject_types !== []) {
            $builder->whereIn('audit_logs.subject_type', $query->subject_types);
        }

        if ($query->search !== null) {
            $builder->where(function (Builder $searchQuery) use ($query): void {
                $searchTerm = '%'.$query->search.'%';

                $searchQuery
                    ->where('audit_logs.summary', 'like', $searchTerm)
                    ->orWhere('audit_logs.event', 'like', $searchTerm)
                    ->orWhere('audit_logs.actor_label', 'like', $searchTerm)
                    ->orWhere('audit_logs.subject_label', 'like', $searchTerm);
            });
        }

        if ($query->from !== null) {
            $builder->where('audit_logs.created_at', '>=', CarbonImmutable::parse($query->from)->startOfDay());
        }

        if ($query->until !== null) {
            $builder->where('audit_logs.created_at', '<=', CarbonImmutable::parse($query->until)->endOfDay());
        }
    }

    /** @param  Builder<AuditLog>  $builder */
    private static function applySorting(Builder $builder, AuditLogIndexQueryData $query): void
    {
        match ($query->sort) {
            'actor' => $builder
                ->orderBy('audit_logs.actor_label', $query->direction)
                ->orderBy('audit_logs.created_at', 'desc')
                ->orderBy('audit_logs.id', 'desc'),
            'event' => $builder
                ->orderBy('audit_logs.event', $query->direction)
                ->orderBy('audit_logs.created_at', 'desc')
                ->orderBy('audit_logs.id', 'desc'),
            'subject' => $builder
                ->orderBy('audit_logs.subject_label', $query->direction)
                ->orderBy('audit_logs.created_at', 'desc')
                ->orderBy('audit_logs.id', 'desc'),
            default => $builder
                ->orderBy('audit_logs.created_at', $query->direction)
                ->orderBy('audit_logs.id', $query->direction),
        };
    }
}
