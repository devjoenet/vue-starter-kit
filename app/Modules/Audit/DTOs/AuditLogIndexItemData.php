<?php

declare(strict_types=1);

namespace App\Modules\Audit\DTOs;

use App\Modules\Audit\Models\AuditLog;
use Carbon\CarbonInterface;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class AuditLogIndexItemData extends Data
{
    public function __construct(
        public int $id,
        public string $created_at,
        public string $event,
        public string $summary,
        public ?string $actor_label,
        public ?string $subject_type,
        public ?int $subject_id,
        public ?string $subject_label,
        public ?string $method,
        public ?string $url,
        public ?string $ip_address,
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
            subject_type: $auditLog->subject_type,
            subject_id: $auditLog->subject_id,
            subject_label: $auditLog->subject_label,
            method: $auditLog->method,
            url: $auditLog->url,
            ip_address: $auditLog->ip_address,
        );
    }
}
