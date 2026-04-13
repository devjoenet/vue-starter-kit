<?php

declare(strict_types=1);

namespace App\Modules\IAM\Permissions\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
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
#[Fillable([
    'slug',
    'label',
    'description',
])]
class PermissionGroup extends Model
{
    use SoftDeletes;

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
