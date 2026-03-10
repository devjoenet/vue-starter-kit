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
use App\Support\AdminIndexQuery;
use App\Support\Data\Admin\AdminIndexQueryData;
use App\Support\Data\Admin\Permissions\CreatePermissionData;
use App\Support\Data\Admin\Permissions\PermissionIndexFilterOptionsData;
use App\Support\Data\Admin\Permissions\PermissionIndexItemData;
use App\Support\Data\Admin\Permissions\PermissionItemData;
use App\Support\Data\Admin\Permissions\UpdatePermissionData;
use App\Support\GroupedPermissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class PermissionsController extends Controller
{
    public function index(Request $request): Response
    {
        $indexQuery = AdminIndexQuery::fromRequest(
            request: $request,
            allowedSorts: ['id', 'group', 'permission', 'permission_check'],
            allowedFilters: ['group', 'permission', 'permission_check'],
        );

        $permissions = Permission::query()
            ->select(['id', 'name', 'group'])
            ->get()
            ->map(fn (Permission $permission): array => PermissionIndexItemData::fromModel($permission)->all());

        $filteredPermissions = $permissions
            ->when(
                $indexQuery->filterValues('group') !== [],
                fn ($collection) => $collection->whereIn('group', $indexQuery->filterValues('group')),
            )
            ->when(
                $indexQuery->filterValues('permission') !== [],
                fn ($collection) => $collection->whereIn('suffix', $indexQuery->filterValues('permission')),
            )
            ->when(
                $indexQuery->filterValues('permission_check') !== [],
                fn ($collection) => $collection->whereIn('name', $indexQuery->filterValues('permission_check')),
            );

        $sortedPermissions = match ($indexQuery->sort) {
            'group' => $filteredPermissions->sortBy('group', SORT_NATURAL, $indexQuery->direction === 'desc'),
            'permission' => $filteredPermissions->sortBy('suffix', SORT_NATURAL, $indexQuery->direction === 'desc'),
            'permission_check' => $filteredPermissions->sortBy('name', SORT_NATURAL, $indexQuery->direction === 'desc'),
            default => $filteredPermissions->sortBy('id', SORT_NUMERIC, $indexQuery->direction === 'desc'),
        };

        return Inertia::render('admin/Permissions/Index', [
            'permissions' => $sortedPermissions->values()->all(),
            'filterOptions' => PermissionIndexFilterOptionsData::from([
                'group' => $permissions->pluck('group')->unique()->sort()->values()->all(),
                'permission' => $permissions->pluck('suffix')->unique()->sort()->values()->all(),
                'permission_check' => $permissions->pluck('name')->unique()->sort()->values()->all(),
            ])->all(),
            'query' => AdminIndexQueryData::fromQuery($indexQuery)->all(),
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
