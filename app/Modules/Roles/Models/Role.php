<?php

declare(strict_types=1);

namespace App\Modules\Roles\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @property string $name
 * @property string $guard_name
 * @property Carbon|null $deleted_at
 */
class Role extends SpatieRole
{
    use SoftDeletes;
}
