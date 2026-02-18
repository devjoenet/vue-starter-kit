<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Data\Admin\PermissionUpsertData;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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

    public function store(): RedirectResponse
    {
        $data = PermissionUpsertData::validateAndCreate(request());

        $group = $this->normalizePermissionGroup($data->group);
        $name = $this->normalizePermissionName($group, $data->name);

        request()->merge([
            'name' => $name,
            'group' => $group,
        ]);

        request()->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')],
            'group' => ['required', 'string', 'max:255'],
        ]);

        $permission = Permission::query()->create([
            'name' => $name,
            'group' => $group,
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

    public function update(Permission $permission): RedirectResponse
    {
        $data = PermissionUpsertData::validateAndCreate(request());

        $group = $this->normalizePermissionGroup($data->group);
        $name = $this->normalizePermissionName($group, $data->name);

        request()->merge([
            'name' => $name,
            'group' => $group,
        ]);

        request()->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
            'group' => ['required', 'string', 'max:255'],
        ]);

        $permission->forceFill([
            'name' => $name,
            'group' => $group,
        ])->save();

        return back()->with('success', 'Permission updated.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted.');
    }

    private function normalizePermissionName(string $group, string $name): string
    {
        $segment = mb_trim($name);

        if (str_contains($segment, '.')) {
            $parts = array_values(array_filter(explode('.', $segment)));
            $segment = (string) (end($parts) ?: '');
        }

        $action = Str::of($segment)->squish()->camel()->toString();

        if ($action === '') {
            return '';
        }

        return sprintf('%s.%s', $group, $action);
    }

    private function normalizePermissionGroup(string $group): string
    {
        return Str::of($group)
            ->trim()
            ->replaceMatches('/[^A-Za-z0-9]+/', '_')
            ->lower()
            ->trim('_')
            ->toString();
    }
}
