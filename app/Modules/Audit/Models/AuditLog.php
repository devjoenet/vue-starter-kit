<?php

declare(strict_types=1);

namespace App\Modules\Audit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Override;

/**
 * @property array<string, mixed>|null $changes
 * @property array<string, mixed>|null $context
 */
class AuditLog extends Model
{
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'actor_type',
        'actor_id',
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
    ];

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
