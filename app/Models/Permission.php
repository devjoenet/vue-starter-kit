<?php

declare(strict_types=1);

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * @property string $group
 */
class Permission extends SpatiePermission
{
    /** @var list<string> */
    protected $fillable = [
        'name',
        'guard_name',
        'group',
    ];
}
