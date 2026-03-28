<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Users\CreateUser;
use App\Actions\Admin\Users\DeleteUser;
use App\Actions\Admin\Users\GetEditableRoles;
use App\Actions\Admin\Users\IndexUsers;
use App\Actions\Admin\Users\SyncUserRoles;
use App\Actions\Admin\Users\UpdateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\SyncUserRolesRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Support\Data\Admin\Users\CreateUserData;
use App\Support\Data\Admin\Users\EditableUserData;
use App\Support\Data\Admin\Users\SyncUserRolesData;
use App\Support\Data\Admin\Users\UpdateUserData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class UsersController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('admin/Users/Index', IndexUsers::handle($request));
    }

    public function create(): Response
    {
        return Inertia::render('admin/Users/Create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = CreateUser::handle(new CreateUserData(
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
            'roles' => Inertia::defer(fn (): array => GetEditableRoles::handle()),
            'userRoles' => fn (): array => $user->getRoleNames()->values()->all(),
        ]);
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
    ): RedirectResponse {
        UpdateUser::handle($user, new UpdateUserData(
            name: (string) $request->validated('name'),
            email: (string) $request->validated('email'),
            password: $request->validated('password'),
        ));

        if ($request->boolean('quiet_success')) {
            return back();
        }

        return $this->backWithSuccess('User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        DeleteUser::handle($user);

        return $this->redirectRouteWithSuccess('admin.users.index', [], 'User deleted.');
    }

    public function syncRoles(
        SyncUserRolesRequest $request,
        User $user,
    ): RedirectResponse {
        /** @var list<string> $roleNames */
        $roleNames = $request->validated('roles', []);

        SyncUserRoles::handle($user, new SyncUserRolesData(roles: $roleNames));

        if ($request->boolean('quiet_success')) {
            return back();
        }

        return $this->backWithSuccess('Roles updated.');
    }
}
