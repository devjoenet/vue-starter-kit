<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Permissions\CreatePermission;
use App\Actions\Admin\Permissions\DeletePermission;
use App\Actions\Admin\Permissions\UpdatePermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Models\Permission;
use App\Support\Data\Admin\Permissions\CreatePermissionData;
use App\Support\Data\Admin\Permissions\PermissionItemData;
use App\Support\Data\Admin\Permissions\UpdatePermissionData;
use App\Support\GroupedPermissions;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class PermissionsController extends Controller
{
    public function index(GroupedPermissions $groupedPermissions): Response
    {
        return Inertia::render('admin/Permissions/Index', [
            'permissionsByGroup' => fn (): array => $groupedPermissions->allData(),
        ]);
    }

    public function create(GroupedPermissions $groupedPermissions): Response
    {
        return Inertia::render('admin/Permissions/Create', [
            'groups' => $groupedPermissions->groups(),
        ]);
    }

    public function store(
        StorePermissionRequest $request,
        CreatePermission $createPermission,
    ): RedirectResponse {
        $permission = $createPermission->handle(new CreatePermissionData(
            name: (string) $request->validated('name'),
            group: (string) $request->validated('group'),
        ));

        return $this->redirectRouteWithSuccess(
            'admin.permissions.edit',
            $permission,
            'Permission created.',
        );
    }

    public function edit(Permission $permission, GroupedPermissions $groupedPermissions): Response
    {
        $groups = collect($groupedPermissions->groups())
            ->push($permission->group)
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return Inertia::render('admin/Permissions/Edit', [
            'permission' => PermissionItemData::fromModel($permission)->all(),
            'groups' => $groups,
        ]);
    }

    public function update(
        UpdatePermissionRequest $request,
        Permission $permission,
        UpdatePermission $updatePermission,
    ): RedirectResponse {
        $updatePermission->handle($permission, new UpdatePermissionData(
            name: (string) $request->validated('name'),
            group: (string) $request->validated('group'),
        ));

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
