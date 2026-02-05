<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Data\Admin\UserUpsertData;
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
        return $this->renderIndex();
    }

    public function create(): Response
    {
        return $this->renderIndex([
            'modal' => [
                'mode' => 'create',
            ],
        ]);
    }

    public function store(): RedirectResponse
    {
        $data = UserUpsertData::validateAndCreate(request());

        $user = User::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password ?? str()->password(16)),
        ]);

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'User created.');
    }

    public function edit(User $user): Response
    {
        return $this->renderIndex([
            'modal' => [
                'mode' => 'edit',
                'user' => $user->only(['id', 'name', 'email']),
                'userRoles' => $user->getRoleNames(),
            ],
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(User $user): RedirectResponse
    {
        $data = UserUpsertData::validateAndCreate(
            request()->merge(['password' => request()->string('password')->toString() ?: null])
        );

        $user->forceFill([
            'name' => $data->name,
            'email' => $data->email,
        ]);

        if ($data->password) {
            $user->forceFill(['password' => Hash::make($data->password)]);
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

    public function syncRoles(User $user): RedirectResponse
    {
        $roleNames = request()->input('roles', []);
        $user->syncRoles($roleNames);

        return back()->with('success', 'Roles updated.');
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function renderIndex(array $extra = []): Response
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::query()
                ->select(['id', 'name', 'email', 'created_at'])
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            ...$extra,
        ]);
    }
}
