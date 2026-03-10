<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Roles\CreateRole;
use App\Actions\Admin\Roles\DeleteRole;
use App\Actions\Admin\Roles\SyncRolePermissions;
use App\Actions\Admin\Roles\UpdateRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\SyncRolePermissionsRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminIndexQuery;
use App\Support\Data\Admin\AdminIndexQueryData;
use App\Support\Data\Admin\Roles\AssignableUserData;
use App\Support\Data\Admin\Roles\CreateRoleData;
use App\Support\Data\Admin\Roles\EditableRoleData;
use App\Support\Data\Admin\Roles\RoleIndexFilterOptionsData;
use App\Support\Data\Admin\Roles\RoleListItemData;
use App\Support\Data\Admin\Roles\SyncRolePermissionsData;
use App\Support\Data\Admin\Roles\UpdateRoleData;
use App\Support\GroupedPermissions;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class RolesController extends Controller
{
    public function index(Request $request): Response
    {
        $indexQuery = AdminIndexQuery::fromRequest(
            request: $request,
            allowedSorts: ['id', 'display_name', 'slug', 'users'],
            allowedFilters: ['display_name', 'slug', 'users'],
        );

        $roles = Role::query()
            ->select(['id', 'name'])
            ->withCount('users')
            ->get()
            ->map(fn (Role $role): array => RoleListItemData::fromModel($role)->all());

        $filteredRoles = $roles
            ->when(
                $indexQuery->filterValues('display_name') !== [],
                fn ($collection) => $collection->whereIn('name', $indexQuery->filterValues('display_name')),
            )
            ->when(
                $indexQuery->filterValues('slug') !== [],
                fn ($collection) => $collection->whereIn('name', $indexQuery->filterValues('slug')),
            )
            ->when(
                $indexQuery->filterValues('users') !== [],
                fn ($collection) => $collection->filter(
                    fn (array $role): bool => in_array((string) $role['users_count'], $indexQuery->filterValues('users'), true),
                ),
            );

        $sortedRoles = match ($indexQuery->sort) {
            'display_name', 'slug' => $filteredRoles->sortBy('name', SORT_NATURAL, $indexQuery->direction === 'desc'),
            'users' => $filteredRoles->sortBy('users_count', SORT_NUMERIC, $indexQuery->direction === 'desc'),
            default => $filteredRoles->sortBy('id', SORT_NUMERIC, $indexQuery->direction === 'desc'),
        };

        return Inertia::render('admin/Roles/Index', [
            'roles' => $sortedRoles->values()->all(),
            'filterOptions' => RoleIndexFilterOptionsData::from([
                'display_name' => $roles->pluck('name')->unique()->sort()->values()->all(),
                'slug' => $roles->pluck('name')->unique()->sort()->values()->all(),
                'users' => $roles->pluck('users_count')
                    ->map(fn (int $count): string => (string) $count)
                    ->unique()
                    ->sort()
                    ->values()
                    ->all(),
            ])->all(),
            'query' => AdminIndexQueryData::fromQuery($indexQuery)->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Roles/Create', [
            'users' => User::query()
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->orderBy('email')
                ->get()
                ->map(fn (User $user): array => AssignableUserData::fromModel($user)->all())
                ->all(),
        ]);
    }

    public function store(StoreRoleRequest $request, CreateRole $createRole): RedirectResponse
    {
        /** @var list<int> $userIds */
        $userIds = $request->validated('user_ids', []);

        $role = $createRole->handle(new CreateRoleData(
            name: (string) $request->validated('name'),
            user_ids: $userIds,
        ));

        return $this->redirectRouteWithSuccess('admin.roles.edit', $role, 'Role created.');
    }

    public function edit(Role $role, GroupedPermissions $groupedPermissions): Response
    {
        return Inertia::render('admin/Roles/Edit', [
            'role' => fn (): array => EditableRoleData::fromModel($role)->all(),
            'permissionsByGroup' => $groupedPermissions->allData(...),
            'rolePermissions' => fn (): array => $role->permissions()->pluck('name')->values()->all(),
        ]);
    }

    public function update(
        UpdateRoleRequest $request,
        Role $role,
        UpdateRole $updateRole,
    ): RedirectResponse {
        $updateRole->handle($role, new UpdateRoleData(
            name: (string) $request->validated('name'),
        ));

        if ($request->boolean('quiet_success')) {
            return back();
        }

        return $this->backWithSuccess('Role updated.');
    }

    public function destroy(Role $role, DeleteRole $deleteRole): RedirectResponse
    {
        $deleteRole->handle($role);

        return $this->redirectRouteWithSuccess('admin.roles.index', [], 'Role deleted.');
    }

    public function syncPermissions(
        SyncRolePermissionsRequest $request,
        Role $role,
        SyncRolePermissions $syncRolePermissions,
    ): RedirectResponse {
        /** @var list<string> $permissionNames */
        $permissionNames = $request->validated('permissions', []);

        $syncRolePermissions->handle($role, new SyncRolePermissionsData(
            permissions: $permissionNames,
        ));

        if ($request->boolean('quiet_success')) {
            return back();
        }

        return $this->backWithSuccess('Permissions updated.');
    }
}
