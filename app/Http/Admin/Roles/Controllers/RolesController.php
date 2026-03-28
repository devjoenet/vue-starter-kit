<?php

declare(strict_types=1);

namespace App\Http\Admin\Roles\Controllers;

use App\Http\Admin\Roles\Requests\StoreRoleRequest;
use App\Http\Admin\Roles\Requests\SyncRolePermissionsRequest;
use App\Http\Admin\Roles\Requests\UpdateRoleRequest;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Modules\Admin\Roles\Actions\CreateRole;
use App\Modules\Admin\Roles\Actions\DeleteRole;
use App\Modules\Admin\Roles\Actions\SyncRolePermissions;
use App\Modules\Admin\Roles\Actions\UpdateRole;
use App\Modules\Admin\Roles\DTOs\CreateRoleData;
use App\Modules\Admin\Roles\DTOs\EditableRoleData;
use App\Modules\Admin\Roles\DTOs\SyncRolePermissionsData;
use App\Modules\Admin\Roles\DTOs\UpdateRoleData;
use App\Modules\Admin\Roles\Queries\GetAssignableUsers;
use App\Modules\Admin\Roles\Queries\IndexRoles;
use App\Modules\Admin\Roles\Support\GroupedPermissions;
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
