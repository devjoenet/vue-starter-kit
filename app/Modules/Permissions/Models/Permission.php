<?php

declare(strict_types=1);

namespace App\Modules\Permissions\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * @property string $name
 * @property string|null $label
 * @property string|null $description
 * @property int|null $permission_group_id
 * @property Carbon|null $deleted_at
 * @property-read string $group
 * @property-read string $group_label
 * @property-read string|null $group_description
 * @property-read string $action
 * @property-read string $display_label
 * @property-read PermissionGroup|null $permissionGroup
 */
#[Fillable([
    'permission_group_id',
    'name',
    'label',
    'description',
    'guard_name',
])]
class Permission extends SpatiePermission
{
    use SoftDeletes;

    public function permissionGroup(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class);
    }

    /** Accessor for the permission group's canonical slug. */
    protected function group(): Attribute
    {
        return Attribute::get(function (): string {
            $group = $this->permissionGroup;

            if ($group instanceof PermissionGroup) {
                return $group->slug;
            }

            return Str::beforeLast($this->name, '.');
        });
    }

    /** Accessor for the human-facing label of the permission group. */
    protected function groupLabel(): Attribute
    {
        return Attribute::get(function (): string {
            $group = $this->permissionGroup;

            if ($group instanceof PermissionGroup) {
                return $group->label;
            }

            return Str::of($this->group)->replace('_', ' ')->title()->toString();
        });
    }

    /** Accessor for the permission group's descriptive copy. */
    protected function groupDescription(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->permissionGroup?->description,
        );
    }

    /** Accessor for the terminal action segment of a permission key. */
    protected function action(): Attribute
    {
        return Attribute::get(function (): string {
            $segments = array_values(array_filter(explode('.', $this->name)));

            return (string) (end($segments) ?: $this->name);
        });
    }

    /** Accessor for the preferred display label shown in the admin catalog. */
    protected function displayLabel(): Attribute
    {
        return Attribute::get(function (): string {
            if (filled($this->label)) {
                return (string) $this->label;
            }

            $headlineReady = preg_replace('/([a-z])([A-Z])/', '$1 $2', $this->action) ?? $this->action;

            return Str::of($headlineReady)
                ->replace(['_', '.'], ' ')
                ->headline()
                ->toString();
        });
    }
}
