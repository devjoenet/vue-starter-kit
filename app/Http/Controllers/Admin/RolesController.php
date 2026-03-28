<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Roles\CreateRole;
use App\Actions\Admin\Roles\DeleteRole;
use App\Actions\Admin\Roles\GetAssignableUsers;
use App\Actions\Admin\Roles\IndexRoles;
use App\Actions\Admin\Roles\SyncRolePermissions;
use App\Actions\Admin\Roles\UpdateRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\SyncRolePermissionsRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Role;
use App\Support\Data\Admin\Roles\CreateRoleData;
use App\Support\Data\Admin\Roles\EditableRoleData;
use App\Support\Data\Admin\Roles\SyncRolePermissionsData;
use App\Support\Data\Admin\Roles\UpdateRoleData;
use App\Support\GroupedPermissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class RolesController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('admin/Roles/Index', IndexRoles::handle($request));
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

    public function edit(Role $role, GroupedPermissions $groupedPermissions): Response
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
