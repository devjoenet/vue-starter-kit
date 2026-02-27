<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\SyncRolePermissionsRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

final class RolesController
{
    public function index(): Response
    {
        return Inertia::render('admin/Roles/Index', [
            'roles' => Role::query()
                ->select(['id', 'name'])
                ->withCount('users')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Roles/Create', [
            'users' => User::query()
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->orderBy('email')
                ->get(),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $name = $validated['name'];

        $role = Role::query()->create([
            'name' => $name,
            'guard_name' => 'web',
        ]);

        $role->users()->sync($validated['user_ids'] ?? []);

        return redirect()->route('admin.roles.edit', $role)
            ->with('success', 'Role created.');
    }

    public function edit(Role $role): Response
    {
        $permissions = Permission::query()
            ->select(['id', 'name', 'group'])
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group')
            ->map(fn ($items) => $items->values());

        return Inertia::render('admin/Roles/Edit', [
            'roleId' => $role->id,
            'roleName' => $role->name,
            'permissionsByGroup' => $permissions,
            'rolePermissions' => $role->permissions()->pluck('name')->values(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $validated = $request->validated();
        $name = $validated['name'];

        $role->forceFill(['name' => $name])->save();

        return back()->with('success', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted.');
    }

    public function syncPermissions(SyncRolePermissionsRequest $request, Role $role): RedirectResponse
    {
        $permissionNames = $request->validated('permissions', []);

        $existing = Permission::query()
            ->whereIn('name', $permissionNames)
            ->pluck('name')
            ->all();

        $role->syncPermissions($existing);

        return back()->with('success', 'Permissions updated.');
    }
}
