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
use App\Support\PermissionGroupCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class PermissionsController extends Controller
{
    public function __construct(
        private readonly PermissionGroupCatalog $permissionGroupCatalog,
    ) {}

    public function index(Request $request): Response
    {
        $indexQuery = AdminIndexQuery::fromRequest(
            request: $request,
            allowedSorts: ['id', 'group', 'permission', 'permission_check'],
            allowedFilters: ['group', 'permission', 'permission_check'],
        );

        $permissions = Permission::query()
            ->with('permissionGroup')
            ->select([
                'id',
                'permission_group_id',
                'name',
                'label',
                'description',
            ])
            ->get()
            ->map(fn (Permission $permission): array => PermissionIndexItemData::fromModel($permission)->all());

        $filteredPermissions = $permissions
            ->when(
                $indexQuery->filterValues('group') !== [],
                fn ($collection) => $collection->whereIn('group', $indexQuery->filterValues('group')),
            )
            ->when(
                $indexQuery->filterValues('permission') !== [],
                fn ($collection) => $collection->whereIn('label', $indexQuery->filterValues('permission')),
            )
            ->when(
                $indexQuery->filterValues('permission_check') !== [],
                fn ($collection) => $collection->whereIn('name', $indexQuery->filterValues('permission_check')),
            );

        $sortedPermissions = match ($indexQuery->sort) {
            'group' => $filteredPermissions->sortBy(
                fn (array $permission): string => sprintf('%s|%s', $permission['group_label'], $permission['group']),
                SORT_NATURAL,
                $indexQuery->direction === 'desc',
            ),
            'permission' => $filteredPermissions->sortBy('label', SORT_NATURAL, $indexQuery->direction === 'desc'),
            'permission_check' => $filteredPermissions->sortBy('name', SORT_NATURAL, $indexQuery->direction === 'desc'),
            default => $filteredPermissions->sortBy('id', SORT_NUMERIC, $indexQuery->direction === 'desc'),
        };

        return Inertia::render('admin/Permissions/Index', [
            'permissions' => $sortedPermissions->values()->all(),
            'groups' => $this->permissionGroupCatalog->options(),
            'filterOptions' => PermissionIndexFilterOptionsData::from([
                'group' => $permissions->pluck('group')->unique()->sort()->values()->all(),
                'permission' => $permissions->pluck('label')->unique()->sort()->values()->all(),
                'permission_check' => $permissions->pluck('name')->unique()->sort()->values()->all(),
            ])->all(),
            'query' => AdminIndexQueryData::fromQuery($indexQuery)->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Permissions/Create', [
            'groups' => $this->permissionGroupCatalog->options(),
        ]);
    }

    public function store(
        StorePermissionRequest $request,
        CreatePermission $createPermission,
    ): RedirectResponse {
        $permission = $createPermission->handle(new CreatePermissionData(
            name: (string) $request->validated('name'),
            label: (string) $request->validated('label'),
            description: $request->validated('description'),
            group: (string) $request->validated('group'),
            groupLabel: (string) $request->validated('group_label'),
            groupDescription: $request->validated('group_description'),
        ));

        return $this->redirectRouteWithSuccess(
            'admin.permissions.edit',
            $permission,
            'Permission created.',
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
