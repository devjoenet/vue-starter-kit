<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\SyncRolePermissionsRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Modules\Roles\Actions\CreateRole;
use App\Modules\Roles\Actions\DeleteRole;
use App\Modules\Roles\Actions\GetAssignableUsers;
use App\Modules\Roles\Actions\IndexRoles;
use App\Modules\Roles\Actions\SyncRolePermissions;
use App\Modules\Roles\Actions\UpdateRole;
use App\Modules\Roles\Contracts\GroupedPermissionsProvider;
use App\Modules\Roles\Contracts\RoleFilterOptionsProvider;
use App\Modules\Roles\DTOs\CreateRoleData;
use App\Modules\Roles\DTOs\EditableRoleData;
use App\Modules\Roles\DTOs\SyncRolePermissionsData;
use App\Modules\Roles\DTOs\UpdateRoleData;
use App\Modules\Roles\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class RolesController extends Controller
{
    public function index(
        Request $request,
        RoleFilterOptionsProvider $roleFilterOptionsProvider,
    ): Response {
        return Inertia::render('admin/Roles/Index', IndexRoles::handle($request, $roleFilterOptionsProvider));
    }

    public function create(): Response
    {
        return Inertia::render('admin/Roles/Create', [
            'users' => GetAssignableUsers::handle(),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        /** @var list<int> $userIds */
        $userIds = $request->validated('user_ids', []);

        $role = CreateRole::handle(new CreateRoleData(
            name: (string) $request->validated('name'),
            user_ids: $userIds,
        ));

        return $this->redirectRouteWithSuccess('admin.roles.edit', $role, 'Role created.');
    }

    public function edit(Role $role, GroupedPermissionsProvider $groupedPermissions): Response
    {
        return Inertia::render('admin/Roles/Edit', [
            'role' => fn (): array => EditableRoleData::fromModel($role)->all(),
            'permissionsByGroup' => Inertia::defer(fn (): array => $groupedPermissions->allData()),
            'rolePermissions' => fn (): array => $role->permissions()->pluck('name')->values()->all(),
        ]);
    }

    public function update(
        UpdateRoleRequest $request,
        Role $role,
    ): RedirectResponse {
        UpdateRole::handle($role, new UpdateRoleData(
            name: (string) $request->validated('name'),
        ));

        if ($request->boolean('quiet_success')) {
            return back();
        }

        return $this->backWithSuccess('Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        DeleteRole::handle($role);

        return $this->redirectRouteWithSuccess('admin.roles.index', [], 'Role deleted.');
    }

    public function syncPermissions(
        SyncRolePermissionsRequest $request,
        Role $role,
    ): RedirectResponse {
        /** @var list<string> $permissionNames */
        $permissionNames = $request->validated('permissions', []);

        SyncRolePermissions::handle($role, new SyncRolePermissionsData(
            permissions: $permissionNames,
        ));

        if ($request->boolean('quiet_success')) {
            return back();
        }

        return $this->backWithSuccess('Permissions updated.');
    }
}
