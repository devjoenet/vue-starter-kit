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
use App\Support\Data\Admin\Users\CreateUserData;
use App\Support\Data\Admin\Users\EditableUserData;
use App\Support\Data\Admin\Users\RoleOptionData;
use App\Support\Data\Admin\Users\SyncUserRolesData;
use App\Support\Data\Admin\Users\UpdateUserData;
use App\Support\Data\Admin\Users\UserListItemData;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class UsersController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Users/Index', [
            'users' => User::query()
                ->select(['id', 'name', 'email'])
                ->with(['roles:id,name'])
                ->latest()
                ->paginate(15)
                ->through(fn (User $user): array => UserListItemData::fromModel($user)->all())
                ->withQueryString(),
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

        return $this->backWithSuccess('Roles updated.');
    }
}
