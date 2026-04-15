<?php

declare(strict_types=1);

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Audit\Actions\GetAuditHistoryItems;
use Modules\Core\Models\User;
use Modules\Roles\Actions\GetEditableRoles;
use Modules\Users\Actions\CreateUser;
use Modules\Users\Actions\DeleteUser;
use Modules\Users\Actions\IndexUsers;
use Modules\Users\Actions\SyncUserRoles;
use Modules\Users\Actions\UpdateUser;
use Modules\Users\Contracts\UserFilterOptionsProvider;
use Modules\Users\DTOs\CreateUserData;
use Modules\Users\DTOs\EditableUserData;
use Modules\Users\DTOs\SyncUserRolesData;
use Modules\Users\DTOs\UpdateUserData;
use Modules\Users\Http\Requests\StoreUserRequest;
use Modules\Users\Http\Requests\SyncUserRolesRequest;
use Modules\Users\Http\Requests\UpdateUserRequest;

final class UsersController extends Controller
{
    #[Authorize('users.view')]
    public function index(Request $request, UserFilterOptionsProvider $filters): Response
    {
        return Inertia::render('Users/Index', IndexUsers::handle($request, $filters));
    }

    #[Authorize('users.create')]
    public function create(): Response
    {
        return Inertia::render('Users/Create');
    }

    #[Authorize('users.create')]
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = CreateUser::handle(
            CreateUserData::fromInput($request->safe()->only(['name', 'email', 'password'])),
        );

        return $this->redirectRouteWithSuccess('admin.users.edit', $user, 'User '.$user->name.' created.');
    }

    #[Authorize('users.update')]
    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'user' => fn (): EditableUserData => EditableUserData::fromModel($user),
            'roles' => Inertia::defer(fn (): Collection => GetEditableRoles::handle()),
            'auditHistory' => Inertia::defer(fn (): Collection => GetAuditHistoryItems::handle($user)),
            'userRoles' => fn (): array => $user->getRoleNames()->values()->all(),
        ]);
    }

    #[Authorize('users.update')]
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        UpdateUser::handle($user, UpdateUserData::fromInput($request->safe()->only(['name', 'email', 'password'])));

        return $request->boolean('quiet_success') ? back() : $this->backWithSuccess('User updated.');
    }

    #[Authorize('users.delete')]
    public function destroy(User $user): RedirectResponse
    {
        DeleteUser::handle($user);

        return $this->redirectRouteWithSuccess('admin.users.index', [], 'User deleted.');
    }

    #[Authorize('users.assignRoles')]
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
