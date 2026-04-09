<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\IAM\Actions\CreateUser;
use App\Modules\IAM\Actions\DeleteUser;
use App\Modules\IAM\Actions\GetEditableRoles;
use App\Modules\IAM\Actions\IndexUsers;
use App\Modules\IAM\Actions\SyncUserRoles;
use App\Modules\IAM\Actions\UpdateUser;
use App\Modules\IAM\Contracts\UserFilterOptionsProvider;
use App\Modules\IAM\DTOs\CreateUserData;
use App\Modules\IAM\DTOs\EditableUserData;
use App\Modules\IAM\DTOs\SyncUserRolesData;
use App\Modules\IAM\DTOs\UpdateUserData;
use App\Modules\IAM\Requests\StoreUserRequest;
use App\Modules\IAM\Requests\SyncUserRolesRequest;
use App\Modules\IAM\Requests\UpdateUserRequest;
use App\Modules\Shared\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

final class UsersController extends Controller
{
    public function index(Request $request, UserFilterOptionsProvider $filters): Response
    {
        return Inertia::render('admin/Users/Index', IndexUsers::handle($request, $filters));
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

        return $this->redirectRouteWithSuccess('admin.users.edit', $user, 'User '.$user->name.' created.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('admin/Users/Edit', [
            'user' => fn (): EditableUserData => EditableUserData::fromModel($user),
            'roles' => Inertia::defer(fn (): Collection => GetEditableRoles::handle()),
            'userRoles' => fn (): array => $user->getRoleNames()->values()->all(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        UpdateUser::handle($user, UpdateUserData::fromInput($request->safe()->only(['name', 'email', 'password'])));

        return $request->boolean('quiet_success') ? back() : $this->backWithSuccess('User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        DeleteUser::handle($user);

        return $this->redirectRouteWithSuccess('admin.users.index', [], 'User deleted.');
    }

    public function syncRoles(SyncUserRolesRequest $request, User $user): RedirectResponse
    {
        SyncUserRoles::handle($user,
            SyncUserRolesData::fromInput([
                'roles' => $request->validated('roles', []),
            ]),
        );

        return $request->boolean('quiet_success') ? back() : $this->backWithSuccess('Roles updated.');
    }
}
