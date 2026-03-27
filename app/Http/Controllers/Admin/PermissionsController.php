<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Permissions\DeletePermission;
use App\Actions\Admin\Permissions\IndexPermissions;
use App\Actions\Admin\Permissions\StorePermission;
use App\Actions\Admin\Permissions\UpdatePermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Models\Permission;
use App\Support\Data\Admin\Permissions\PermissionItemData;
use App\Support\Data\Admin\Permissions\UpdatePermissionData;
use App\Support\PermissionGroupCatalog;
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
        $permission = StorePermission::handle($request);

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
        UpdatePermission $updatePermission,
    ): RedirectResponse {
        $updatePermission->handle($permission, new UpdatePermissionData(
            label: (string) $request->validated('label'),
            description: $request->validated('description'),
            group: (string) $request->validated('group'),
            groupLabel: (string) $request->validated('group_label'),
            groupDescription: $request->validated('group_description'),
        ));

        if ($request->boolean('quiet_success')) {
            return back();
        }

        return $this->backWithSuccess('Permission updated.');
    }

    public function destroy(
        Permission $permission,
        DeletePermission $deletePermission,
    ): RedirectResponse {
        $deletePermission->handle($permission);

        return $this->redirectRouteWithSuccess('admin.permissions.index', [], 'Permission deleted.');
    }
}
