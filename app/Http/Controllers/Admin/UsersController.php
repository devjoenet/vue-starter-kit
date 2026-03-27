<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Users\CreateUser;
use App\Actions\Admin\Users\DeleteUser;
use App\Actions\Admin\Users\SyncUserRoles;
use App\Actions\Admin\Users\UpdateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\SyncUserRolesRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminIndexQuery;
use App\Support\Data\Admin\AdminIndexQueryData;
use App\Support\Data\Admin\Users\CreateUserData;
use App\Support\Data\Admin\Users\EditableUserData;
use App\Support\Data\Admin\Users\RoleOptionData;
use App\Support\Data\Admin\Users\SyncUserRolesData;
use App\Support\Data\Admin\Users\UpdateUserData;
use App\Support\Data\Admin\Users\UserIndexFilterOptionsData;
use App\Support\Data\Admin\Users\UserListItemData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class UsersController extends Controller
{
    public function index(Request $request): Response
    {
        $indexQuery = $this->indexQuery($request);

        return Inertia::render('admin/Users/Index', [
            'users' => $this->users($indexQuery),
            'filterOptions' => $this->userFilterOptions(),
            'query' => AdminIndexQueryData::fromQuery($indexQuery)->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Users/Create');
    }

    public function store(StoreUserRequest $request, CreateUser $createUser): RedirectResponse
    {
        $user = $createUser->handle(new CreateUserData(
            name: (string) $request->validated('name'),
            email: (string) $request->validated('email'),
            password: (string) $request->validated('password'),
        ));

        return $this->redirectRouteWithSuccess('admin.users.edit', $user, 'User created.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('admin/Users/Edit', [
            'user' => fn (): array => EditableUserData::fromModel($user)->all(),
            'roles' => Inertia::defer(fn (): array => $this->editableRoles()),
            'userRoles' => fn (): array => $user->getRoleNames()->values()->all(),
        ]);
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
        UpdateUser $updateUser,
    ): RedirectResponse {
        $updateUser->handle($user, new UpdateUserData(
            name: (string) $request->validated('name'),
            email: (string) $request->validated('email'),
            password: $request->validated('password'),
        ));

        if ($request->boolean('quiet_success')) {
            return back();
        }

        return $this->backWithSuccess('User updated.');
    }

    public function destroy(User $user, DeleteUser $deleteUser): RedirectResponse
    {
        $deleteUser->handle($user);

        return $this->redirectRouteWithSuccess('admin.users.index', [], 'User deleted.');
    }

    public function syncRoles(
        SyncUserRolesRequest $request,
        User $user,
        SyncUserRoles $syncUserRoles,
    ): RedirectResponse {
        /** @var list<string> $roleNames */
        $roleNames = $request->validated('roles', []);

        $syncUserRoles->handle($user, new SyncUserRolesData(roles: $roleNames));

        if ($request->boolean('quiet_success')) {
            return back();
        }

        return $this->backWithSuccess('Roles updated.');
    }

    private function indexQuery(Request $request): AdminIndexQuery
    {
        return AdminIndexQuery::fromRequest(
            request: $request,
            allowedSorts: ['id', 'name', 'email', 'roles'],
            allowedFilters: ['name', 'email', 'roles'],
        );
    }

    private function users(AdminIndexQuery $indexQuery): LengthAwarePaginator
    {
        return $this->usersQuery($indexQuery)
            ->paginate(15)
            ->through(fn (User $user): array => UserListItemData::fromModel($user)->all())
            ->withQueryString();
    }

    private function usersQuery(AdminIndexQuery $indexQuery): Builder
    {
        $nameFilters = $indexQuery->filterValues('name');
        $emailFilters = $indexQuery->filterValues('email');
        $roleFilters = $indexQuery->filterValues('roles');

        $query = User::query()
            ->select(['id', 'name', 'email'])
            ->with(['roles:id,name']);

        if ($nameFilters !== []) {
            $query->whereIn('name', $nameFilters);
        }

        if ($emailFilters !== []) {
            $query->whereIn('email', $emailFilters);
        }

        if ($roleFilters !== []) {
            $query->whereHas('roles', fn (Builder $rolesQuery): Builder => $rolesQuery->whereIn('name', $roleFilters));
        }

        $this->applyUserSorting($query, $indexQuery);

        return $query;
    }

    private function applyUserSorting(Builder $query, AdminIndexQuery $indexQuery): void
    {
        if ($indexQuery->sort === 'roles') {
            $query->withMin('roles as sort_role_name', 'name')
                ->orderBy('sort_role_name', $indexQuery->direction)
                ->orderBy('id');

            return;
        }

        $query->orderBy($this->userSortColumn($indexQuery->sort), $indexQuery->direction)
            ->orderBy('id');
    }

    private function userSortColumn(string $sort): string
    {
        return match ($sort) {
            'name' => 'name',
            'email' => 'email',
            default => 'id',
        };
    }

    private function userFilterOptions(): array
    {
        return UserIndexFilterOptionsData::from([
            'name' => $this->distinctUserValues('name'),
            'email' => $this->distinctUserValues('email'),
            'roles' => Role::query()
                ->select('name')
                ->orderBy('name')
                ->pluck('name')
                ->all(),
        ])->all();
    }

    /**
     * @return list<string>
     */
    private function distinctUserValues(string $column): array
    {
        return User::query()
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->all();
    }

    /**
     * @return list<array{id: int, name: string}>
     */
    private function editableRoles(): array
    {
        return Role::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Role $role): array => RoleOptionData::fromModel($role)->all())
            ->all();
    }
}
