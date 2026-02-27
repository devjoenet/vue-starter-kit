<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\SyncUserRolesRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

final class UsersController
{
    public function index(): Response
    {
        return Inertia::render('admin/Users/Index', [
            'users' => User::query()
                ->select(['id', 'name', 'email', 'created_at'])
                ->with(['roles:id,name'])
                ->latest()
                ->paginate(15)
                ->through(fn (User $user): array => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                    'roles' => $user->roles->pluck('name')->values(),
                ])
                ->withQueryString(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Users/Create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'User created.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('admin/Users/Edit', [
            'user' => $user->only(['id', 'name', 'email']),
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
            'userRoles' => $user->getRoleNames()->values(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (($validated['password'] ?? null) !== null) {
            $user->forceFill(['password' => Hash::make($validated['password'])]);
        }

        $user->save();

        return back()->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted.');
    }

    public function syncRoles(SyncUserRolesRequest $request, User $user): RedirectResponse
    {
        $roleNames = $request->validated('roles', []);
        $user->syncRoles($roleNames);

        return back()->with('success', 'Roles updated.');
    }
}
