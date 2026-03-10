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
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class UsersController extends Controller
{
    public function index(Request $request): Response
    {
        $indexQuery = AdminIndexQuery::fromRequest(
            request: $request,
            allowedSorts: ['id', 'name', 'email', 'roles'],
            allowedFilters: ['name', 'email', 'roles'],
        );

        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->with(['roles:id,name'])
            ->when(
                $indexQuery->filterValues('name') !== [],
                fn ($query) => $query->whereIn('name', $indexQuery->filterValues('name')),
            )
            ->when(
                $indexQuery->filterValues('email') !== [],
                fn ($query) => $query->whereIn('email', $indexQuery->filterValues('email')),
            )
            ->when(
                $indexQuery->filterValues('roles') !== [],
                fn ($query) => $query->whereHas(
                    'roles',
                    fn ($rolesQuery) => $rolesQuery->whereIn('name', $indexQuery->filterValues('roles')),
                ),
            )
            ->when(
                $indexQuery->sort === 'roles',
                fn ($query) => $query
                    ->withMin('roles as sort_role_name', 'name')
                    ->orderBy('sort_role_name', $indexQuery->direction)
                    ->orderBy('id'),
                function ($query) use ($indexQuery) {
                    $sortColumn = match ($indexQuery->sort) {
                        'name' => 'name',
                        'email' => 'email',
                        default => 'id',
                    };

                    $query->orderBy($sortColumn, $indexQuery->direction)->orderBy('id');
                },
            )
            ->paginate(15)
            ->through(fn (User $user): array => UserListItemData::fromModel($user)->all())
            ->withQueryString();

        return Inertia::render('admin/Users/Index', [
            'users' => $users,
            'filterOptions' => UserIndexFilterOptionsData::from([
                'name' => User::query()
                    ->select('name')
                    ->distinct()
                    ->orderBy('name')
                    ->pluck('name')
                    ->all(),
                'email' => User::query()
                    ->select('email')
                    ->distinct()
                    ->orderBy('email')
                    ->pluck('email')
                    ->all(),
                'roles' => Role::query()
                    ->select('name')
                    ->orderBy('name')
                    ->pluck('name')
                    ->all(),
            ])->all(),
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
            'roles' => fn (): array => Role::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (Role $role): array => RoleOptionData::fromModel($role)->all())
                ->all(),
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
}
