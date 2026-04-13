<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Audit\Actions\GetAuditHistoryItems;
use App\Modules\IAM\Roles\Actions\CreateRole;
use App\Modules\IAM\Roles\Actions\DeleteRole;
use App\Modules\IAM\Roles\Actions\IndexRoles;
use App\Modules\IAM\Roles\Actions\SyncRolePermissions;
use App\Modules\IAM\Roles\Actions\UpdateRole;
use App\Modules\IAM\Roles\Contracts\GroupedPermissionsProvider;
use App\Modules\IAM\Roles\Contracts\RoleFilterOptionsProvider;
use App\Modules\IAM\Roles\DTOs\CreateRoleData;
use App\Modules\IAM\Roles\DTOs\EditableRoleData;
use App\Modules\IAM\Roles\DTOs\SyncRolePermissionsData;
use App\Modules\IAM\Roles\DTOs\UpdateRoleData;
use App\Modules\IAM\Roles\Models\Role;
use App\Modules\IAM\Roles\Requests\StoreRoleRequest;
use App\Modules\IAM\Roles\Requests\SyncRolePermissionsRequest;
use App\Modules\IAM\Roles\Requests\UpdateRoleRequest;
use App\Modules\IAM\Users\Actions\GetAssignableUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

final class RolesController extends Controller
{
    #[Authorize('roles.view')]
    public function index(Request $request, RoleFilterOptionsProvider $filters): Response
    {
        return Inertia::render('admin/Roles/Index', IndexRoles::handle($request, $filters));
    }

    #[Authorize('roles.create')]
    public function create(): Response
    {
        return Inertia::render('admin/Roles/Create', [
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
        return Inertia::render('admin/Roles/Edit', [
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
