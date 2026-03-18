<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $slug
 * @property string $label
 * @property string|null $description
 * @property int $permissions_count
 * @property Carbon|null $deleted_at
 */
class PermissionGroup extends Model
{
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'slug',
        'label',
        'description',
    ];

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
