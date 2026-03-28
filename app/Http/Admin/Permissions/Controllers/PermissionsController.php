<?php

declare(strict_types=1);

namespace App\Http\Admin\Permissions\Controllers;

use App\Http\Admin\Permissions\Requests\StorePermissionRequest;
use App\Http\Admin\Permissions\Requests\UpdatePermissionRequest;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Modules\Admin\Permissions\Actions\CreatePermission;
use App\Modules\Admin\Permissions\Actions\DeletePermission;
use App\Modules\Admin\Permissions\Actions\UpdatePermission;
use App\Modules\Admin\Permissions\DTOs\CreatePermissionData;
use App\Modules\Admin\Permissions\DTOs\PermissionItemData;
use App\Modules\Admin\Permissions\DTOs\UpdatePermissionData;
use App\Modules\Admin\Permissions\Queries\IndexPermissions;
use App\Modules\Admin\Permissions\Support\PermissionGroupCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class PermissionsController extends Controller
{
    public function __construct(
        private readonly PermissionGroupCatalog $permissionGroupCatalog
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('admin/Permissions/Index', IndexPermissions::handle($request, $this->permissionGroupCatalog));
    }

    public function create(): Response
    {
        return Inertia::render('admin/Permissions/Create', [
            'groups' => $this->permissionGroupCatalog->options(),
        ]);
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $permission = CreatePermission::handle(new CreatePermissionData(
            name: (string) $request->validated('name'),
            label: (string) $request->validated('label'),
            description: $request->validated('description'),
            group: (string) $request->validated('group'),
            groupLabel: (string) $request->validated('group_label'),
            groupDescription: $request->validated('group_description'),
        ), $this->permissionGroupCatalog);

        return $this->redirectRouteWithSuccess(
            'admin.permissions.edit',
            $permission,
            'Permission '.$permission->label.' created.',
        );
    }

    public function edit(Permission $permission): Response
    {
        $permission->load('permissionGroup');

        return Inertia::render('admin/Permissions/Edit', [
            'permission' => PermissionItemData::fromModel($permission)->all(),
            'groups' => $this->permissionGroupCatalog->options(),
        ]);
    }

    public function update(
        UpdatePermissionRequest $request,
        Permission $permission,
    ): RedirectResponse {
        UpdatePermission::handle($permission, new UpdatePermissionData(
            label: (string) $request->validated('label'),
            description: $request->validated('description'),
            group: (string) $request->validated('group'),
            groupLabel: (string) $request->validated('group_label'),
            groupDescription: $request->validated('group_description'),
        ), $this->permissionGroupCatalog);

        if ($request->boolean('quiet_success')) {
            return back();
        }

        return $this->backWithSuccess('Permission updated.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        DeletePermission::handle($permission);

        return $this->redirectRouteWithSuccess('admin.permissions.index', [], 'Permission deleted.');
    }
}
