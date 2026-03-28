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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

final class RolesController extends Controller
{
    public function index(Request $request): Response
    {
        $indexQuery = $this->indexQuery($request);
        $roles = $this->roleItems();

        return Inertia::render('admin/Roles/Index', [
            'roles' => $this->sortRoles($this->filterRoles($roles, $indexQuery), $indexQuery)->values()->all(),
            'filterOptions' => $this->roleFilterOptions($roles),
            'query' => AdminIndexQueryData::fromQuery($indexQuery)->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Roles/Create', [
            'users' => $this->assignableUsers(),
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

    private function indexQuery(Request $request): AdminIndexQuery
    {
        return AdminIndexQuery::fromRequest(
            request: $request,
            allowedSorts: ['id', 'display_name', 'slug', 'users'],
            allowedFilters: ['display_name', 'slug', 'users'],
        );
    }

    private function roleItems(): Collection
    {
        return Role::query()
            ->select(['id', 'name'])
            ->withCount('users')
            ->get()
            ->map(fn (Role $role): array => RoleListItemData::fromModel($role)->all());
    }

    private function filterRoles(Collection $roles, AdminIndexQuery $indexQuery): Collection
    {
        $displayNameFilters = $indexQuery->filterValues('display_name');
        $slugFilters = $indexQuery->filterValues('slug');
        $userFilters = $indexQuery->filterValues('users');

        if ($displayNameFilters !== []) {
            $roles = $roles->whereIn('name', $displayNameFilters);
        }

        if ($slugFilters !== []) {
            $roles = $roles->whereIn('name', $slugFilters);
        }

        if ($userFilters === []) {
            return $roles;
        }

        return $roles->filter(
            fn (array $role): bool => in_array((string) $role['users_count'], $userFilters, true),
        );
    }

    private function sortRoles(Collection $roles, AdminIndexQuery $indexQuery): Collection
    {
        return match ($indexQuery->sort) {
            'display_name', 'slug' => $roles->sortBy('name', SORT_NATURAL, $indexQuery->direction === 'desc'),
            'users' => $roles->sortBy('users_count', SORT_NUMERIC, $indexQuery->direction === 'desc'),
            default => $roles->sortBy('id', SORT_NUMERIC, $indexQuery->direction === 'desc'),
        };
    }

    private function roleFilterOptions(Collection $roles): array
    {
        return RoleIndexFilterOptionsData::from([
            'display_name' => $roles->pluck('name')->unique()->sort()->values()->all(),
            'slug' => $roles->pluck('name')->unique()->sort()->values()->all(),
            'users' => $roles->pluck('users_count')
                ->map(fn (int $count): string => (string) $count)
                ->unique()
                ->sort()
                ->values()
                ->all(),
        ])->all();
    }

    /**
     * @return list<array{id: int, name: string, email: string}>
     */
    private function assignableUsers(): array
    {
        return User::query()
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->orderBy('email')
            ->get()
            ->map(fn (User $user): array => AssignableUserData::fromModel($user)->all())
            ->all();
    }
}
