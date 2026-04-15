<?php

declare(strict_types=1);

namespace Modules\Roles\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Audit\Actions\GetAuditHistoryItems;
use Modules\Roles\Actions\CreateRole;
use Modules\Roles\Actions\DeleteRole;
use Modules\Roles\Actions\IndexRoles;
use Modules\Roles\Actions\SyncRolePermissions;
use Modules\Roles\Actions\UpdateRole;
use Modules\Roles\Contracts\GroupedPermissionsProvider;
use Modules\Roles\Contracts\RoleFilterOptionsProvider;
use Modules\Roles\DTOs\CreateRoleData;
use Modules\Roles\DTOs\EditableRoleData;
use Modules\Roles\DTOs\SyncRolePermissionsData;
use Modules\Roles\DTOs\UpdateRoleData;
use Modules\Roles\Http\Requests\StoreRoleRequest;
use Modules\Roles\Http\Requests\SyncRolePermissionsRequest;
use Modules\Roles\Http\Requests\UpdateRoleRequest;
use Modules\Roles\Models\Role;
use Modules\Users\Actions\GetAssignableUsers;

final class RolesController extends Controller
{
    #[Authorize('roles.view')]
    public function index(Request $request, RoleFilterOptionsProvider $filters): Response
    {
        return Inertia::render('Roles/Index', IndexRoles::handle($request, $filters));
    }

    #[Authorize('roles.create')]
    public function create(): Response
    {
        return Inertia::render('Roles/Create', [
            'users' => GetAssignableUsers::handle(),
        ]);
    }

    #[Authorize('roles.create')]
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = CreateRole::handle(CreateRoleData::fromRequest($request));

        return $this->redirectRouteWithSuccess('admin.roles.edit', $role, 'Role '.$role->name.' created.');
    }

    #[Authorize('roles.update')]
    public function edit(Role $role, GroupedPermissionsProvider $groupedPermissions): Response
    {
        return Inertia::render('Roles/Edit', [
            'role' => fn (): EditableRoleData => EditableRoleData::fromModel($role),
            'permissionsByGroup' => Inertia::defer(fn (): Collection => $groupedPermissions->allData()),
            'auditHistory' => Inertia::defer(fn (): Collection => GetAuditHistoryItems::handle($role)),
            'rolePermissions' => fn (): array => $role->permissions()->pluck('name')->values()->all(),
        ]);
    }

    #[Authorize('roles.update')]
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        UpdateRole::handle($role, UpdateRoleData::fromRequest($request));

        return $request->boolean('quiet_success') ? back() : $this->backWithSuccess('Role '.$role->name.' updated.');
    }

    #[Authorize('roles.delete')]
    public function destroy(Role $role): RedirectResponse
    {
        DeleteRole::handle($role);

        return $this->redirectRouteWithSuccess('admin.roles.index', [], 'Role '.$role->name.' deleted.');
    }

    #[Authorize('roles.assignPermissions')]
    public function syncPermissions(SyncRolePermissionsRequest $request, Role $role): RedirectResponse
    {
        SyncRolePermissions::handle($role, SyncRolePermissionsData::fromRequest($request));

        return $request->boolean('quiet_success') ? back() : $this->backWithSuccess('Permissions updated.');
    }
}
