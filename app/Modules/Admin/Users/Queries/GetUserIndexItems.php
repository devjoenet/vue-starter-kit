<?php

declare(strict_types=1);

namespace App\Modules\Admin\Users\Queries;

use App\Models\User;
use App\Modules\Admin\Shared\Support\AdminIndexQuery;
use App\Modules\Admin\Users\DTOs\UserListItemData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetUserIndexItems
{
    public static function handle(AdminIndexQuery $indexQuery): LengthAwarePaginator
    {
        return self::query($indexQuery)
            ->paginate(15)
            ->through(fn (User $user): array => UserListItemData::fromModel($user)->all())
            ->withQueryString();
    }

    /**
     * @return Builder<User>
     */
    private static function query(AdminIndexQuery $indexQuery): Builder
    {
        $query = User::query()
            ->select(['users.id', 'users.name', 'users.email'])
            ->with(['roles:id,name']);

        self::applyFilters($query, $indexQuery);
        self::applySorting($query, $indexQuery);

        return $query;
    }

    /**
     * @param  Builder<User>  $query
     */
    private static function applyFilters(Builder $query, AdminIndexQuery $indexQuery): void
    {
        $nameFilters = $indexQuery->filterValues('name');
        $emailFilters = $indexQuery->filterValues('email');
        $roleFilters = $indexQuery->filterValues('roles');

        if ($nameFilters !== []) {
            $query->whereIn('users.name', $nameFilters);
        }

        if ($emailFilters !== []) {
            $query->whereIn('users.email', $emailFilters);
        }

        if ($roleFilters === []) {
            return;
        }

        $query->whereHas('roles', fn (Builder $rolesQuery): Builder => $rolesQuery->whereIn('name', $roleFilters));
    }

    /**
     * @param  Builder<User>  $query
     */
    private static function applySorting(Builder $query, AdminIndexQuery $indexQuery): void
    {
        if ($indexQuery->sort === 'roles') {
            $query->withMin('roles as sort_role_name', 'name')
                ->orderBy('sort_role_name', $indexQuery->direction)
                ->orderBy('users.id');

            return;
        }

        $query->orderBy(self::sortColumn($indexQuery->sort), $indexQuery->direction)
            ->orderBy('users.id');
    }

    private static function sortColumn(string $sort): string
    {
        return match ($sort) {
            'name' => 'users.name',
            'email' => 'users.email',
            default => 'users.id',
        };
    }
}
