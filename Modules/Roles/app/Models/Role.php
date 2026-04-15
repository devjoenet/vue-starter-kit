<?php

declare(strict_types=1);

namespace Modules\Roles\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @property string $name
 * @property string $guard_name
 * @property Carbon|null $deleted_at
 */
#[Table(name: 'roles')]
#[Fillable([
    'name',
    'guard_name',
])]
class Role extends SpatieRole
{
    use SoftDeletes;
}
