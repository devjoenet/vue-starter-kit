<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Actions;

use App\Modules\IAM\Roles\DTOs\RoleListItemData;
use App\Modules\IAM\Roles\Models\Role;
use App\Modules\Shared\Actions\AdminIndexQuery;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

final class GetRoleIndexItems
{
    /** @return Collection<int, RoleListItemData> */
    public static function handle(AdminIndexQuery $indexQuery): Collection
    {
        /** @var Collection<int, RoleListItemData> $roles */
        $roles = RoleListItemData::collect(
            self::query($indexQuery)->get(),
            Collection::class,
        );

        return $roles;
    }

    /** @return Builder<Role> */
    private static function query(AdminIndexQuery $indexQuery): Builder
    {
        $query = Role::query()
            ->select(['roles.id', 'roles.name'])
            ->selectRaw('count(distinct users.id) as users_count')
            ->leftJoin('model_has_roles', function (JoinClause $join): void {
                $join->on('roles.id', '=', 'model_has_roles.role_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->leftJoin('users', function (JoinClause $join): void {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->whereNull('users.deleted_at');
            })
            ->groupBy('roles.id', 'roles.name');

        self::applyFilters($query, $indexQuery);
        self::applySorting($query, $indexQuery);

        return $query;
    }

    /** @param  Builder<Role>  $query */
    private static function applyFilters(Builder $query, AdminIndexQuery $indexQuery): void
    {
        $displayNameFilters = $indexQuery->filterValues('display_name');
        $slugFilters = $indexQuery->filterValues('slug');
        $userFilters = $indexQuery->filterValues('users');

        if ($displayNameFilters !== []) {
            $query->whereIn('roles.name', $displayNameFilters);
        }

        if ($slugFilters !== []) {
            $query->whereIn('roles.name', $slugFilters);
        }

        if ($userFilters === []) {
            return;
        }

        $normalizedUserFilters = array_map(
            static fn (string $count): int => (int) $count,
            $userFilters,
        );
        $placeholders = implode(', ', array_fill(0, count($normalizedUserFilters), '?'));

        $query->havingRaw(sprintf('users_count in (%s)', $placeholders), $normalizedUserFilters);
    }

    /** @param  Builder<Role>  $query */
    private static function applySorting(Builder $query, AdminIndexQuery $indexQuery): void
    {
        $sortColumn = match ($indexQuery->sort) {
            'display_name', 'slug' => 'roles.name',
            'users' => 'users_count',
            default => 'roles.id',
        };

        $query->orderBy($sortColumn, $indexQuery->direction);

        if ($sortColumn !== 'roles.id') {
            $query->orderBy('roles.id');
        }
    }
}
