<?php

declare(strict_types=1);

namespace Modules\Users\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\Actions\AdminIndexQuery;
use Modules\Core\Models\User;
use Modules\Users\DTOs\UserListItemData;

final class GetUserIndexItems
{
    /** @return LengthAwarePaginator<int, UserListItemData> */
    public static function handle(AdminIndexQuery $indexQuery): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, UserListItemData> $users */
        $users = UserListItemData::collect(
            self::query($indexQuery)
                ->paginate(15)
                ->withQueryString(),
        );

        return $users;
    }

    /** @return Builder<User> */
    private static function query(AdminIndexQuery $indexQuery): Builder
    {
        $query = User::query()
            ->select(['users.id', 'users.name', 'users.email'])
            ->with(['roles:id,name']);

        self::applyFilters($query, $indexQuery);
        self::applySorting($query, $indexQuery);

        return $query;
    }

    /** @param  Builder<User>  $query */
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

    /** @param  Builder<User>  $query */
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
