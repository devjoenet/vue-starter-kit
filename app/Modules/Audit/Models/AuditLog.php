<?php

declare(strict_types=1);

namespace App\Modules\Audit\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Override;

/**
 * @property string|null $actor_label
 * @property array<string, mixed>|null $changes
 * @property array<string, mixed>|null $context
 * @property \Carbon\CarbonImmutable|null $created_at
 */
#[WithoutTimestamps]
#[Fillable([
    'actor_type',
    'actor_id',
    'actor_label',
    'event',
    'subject_type',
    'subject_id',
    'subject_label',
    'summary',
    'changes',
    'context',
    'ip_address',
    'user_agent',
    'url',
    'method',
])]
class AuditLog extends Model
{
    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'actor_id' => 'integer',
            'subject_id' => 'integer',
            'changes' => 'array',
            'context' => 'array',
            'created_at' => 'immutable_datetime',
        ];
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
