<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Audit\Actions\GetAuditHistoryItems;
use App\Modules\IAM\Roles\Actions\GetEditableRoles;
use App\Modules\IAM\Users\Actions\CreateUser;
use App\Modules\IAM\Users\Actions\DeleteUser;
use App\Modules\IAM\Users\Actions\IndexUsers;
use App\Modules\IAM\Users\Actions\SyncUserRoles;
use App\Modules\IAM\Users\Actions\UpdateUser;
use App\Modules\IAM\Users\Contracts\UserFilterOptionsProvider;
use App\Modules\IAM\Users\DTOs\CreateUserData;
use App\Modules\IAM\Users\DTOs\EditableUserData;
use App\Modules\IAM\Users\DTOs\SyncUserRolesData;
use App\Modules\IAM\Users\DTOs\UpdateUserData;
use App\Modules\IAM\Users\Requests\StoreUserRequest;
use App\Modules\IAM\Users\Requests\SyncUserRolesRequest;
use App\Modules\IAM\Users\Requests\UpdateUserRequest;
use App\Modules\Shared\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

final class UsersController extends Controller
{
    #[Authorize('users.view')]
    public function index(Request $request, UserFilterOptionsProvider $filters): Response
    {
        return Inertia::render('admin/Users/Index', IndexUsers::handle($request, $filters));
    }

    #[Authorize('users.create')]
    public function create(): Response
    {
        return Inertia::render('admin/Users/Create');
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
        return Inertia::render('admin/Users/Edit', [
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
