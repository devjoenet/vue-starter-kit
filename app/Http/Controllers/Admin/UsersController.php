<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\SyncUserRolesRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Modules\Users\Actions\CreateUser;
use App\Modules\Users\Actions\DeleteUser;
use App\Modules\Users\Actions\GetEditableRoles;
use App\Modules\Users\Actions\IndexUsers;
use App\Modules\Users\Actions\SyncUserRoles;
use App\Modules\Users\Actions\UpdateUser;
use App\Modules\Users\Contracts\UserFilterOptionsProvider;
use App\Modules\Users\DTOs\CreateUserData;
use App\Modules\Users\DTOs\EditableUserData;
use App\Modules\Users\DTOs\SyncUserRolesData;
use App\Modules\Users\DTOs\UpdateUserData;
use App\Modules\Users\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class UsersController extends Controller
{
    public function index(
        Request $request,
        UserFilterOptionsProvider $userFilterOptionsProvider,
    ): Response {
        return Inertia::render('admin/Users/Index', IndexUsers::handle($request, $userFilterOptionsProvider));
    }

    public function create(): Response
    {
        return Inertia::render('admin/Users/Create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = CreateUser::handle(
            CreateUserData::fromInput($request->safe()->only(['name', 'email', 'password'])),
        );

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
        UpdateUser::handle(
            $user,
            UpdateUserData::fromInput($request->safe()->only(['name', 'email', 'password'])),
        );

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
        SyncUserRoles::handle(
            $user,
            SyncUserRolesData::fromInput([
                'roles' => $request->validated('roles', []),
            ]),
        );

        if ($request->boolean('quiet_success')) {
            return back();
        }

        return $this->backWithSuccess('Roles updated.');
    }
}
