<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Data\Admin\PermissionUpsertData;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

final class PermissionsController
{
    public function index(): Response
    {
        return Inertia::render('Admin/Permissions/Index', [
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
        return Inertia::render('Admin/Permissions/Create');
    }

    public function store(): RedirectResponse
    {
        $data = PermissionUpsertData::validateAndCreate(request());

        request()->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')],
            'group' => ['required', 'string', Rule::in(['users', 'roles', 'permissions'])],
        ]);

        $permission = Permission::query()->create([
            'name' => trim($data->name),
            'group' => $data->group,
            'guard_name' => 'web',
        ]);

        return redirect()->route('admin.permissions.edit', $permission)
            ->with('success', 'Permission created.');
    }

    public function edit(Permission $permission): Response
    {
        return Inertia::render('Admin/Permissions/Edit', [
            'permission' => $permission->only(['id', 'name', 'group']),
        ]);
    }

    public function update(Permission $permission): RedirectResponse
    {
        $data = PermissionUpsertData::validateAndCreate(request());

        request()->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
            'group' => ['required', 'string', Rule::in(['users', 'roles', 'permissions'])],
        ]);

        $permission->forceFill([
            'name' => trim($data->name),
            'group' => $data->group,
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
