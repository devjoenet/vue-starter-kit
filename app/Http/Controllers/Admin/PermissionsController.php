<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class PermissionsController
{
    /** @return array<int, string> */
    private function permissionGroups(): array
    {
        return Permission::query()
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->filter()
            ->values()
            ->all();
    }

    public function index(): Response
    {
        return Inertia::render('admin/Permissions/Index', [
            'permissionsByGroup' => Permission::query()
                ->select(['id', 'name', 'group'])
                ->orderBy('group')
                ->orderBy('name')
                ->get()
                ->groupBy('group')
                ->map(fn ($items) => $items->values()),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Permissions/Create', [
            'groups' => $this->permissionGroups(),
        ]);
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $permission = Permission::query()->create([
            'name' => $validated['name'],
            'group' => $validated['group'],
            'guard_name' => 'web',
        ]);

        return redirect()->route('admin.permissions.edit', $permission)
            ->with('success', 'Permission created.');
    }

    public function edit(Permission $permission): Response
    {
        $groups = collect($this->permissionGroups())
            ->push($permission->group)
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return Inertia::render('admin/Permissions/Edit', [
            'permission' => $permission->only(['id', 'name', 'group']),
            'groups' => $groups,
        ]);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validated();

        $permission->forceFill([
            'name' => $validated['name'],
            'group' => $validated['group'],
        ])->save();

        return back()->with('success', 'Permission updated.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted.');
    }
}
