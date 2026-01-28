# Southeast Code Laravel/Vue Admin Starter Plan (laravel/vue-starter-kit)

This document compiles the full step-by-step plan (with copy/paste code blocks) to modify the **laravel/vue-starter-kit** repository into a reusable **Southeast Code Admin Starter** for new projects.

---

## 0) Branch + baseline

```bash
git checkout -b feature/admin-starter
composer install
npm install
php artisan key:generate
php artisan migrate
npm run build
php artisan test
```

---

## 1) Add required packages

### 1.1 Composer require

```bash
composer require spatie/laravel-permission spatie/laravel-data spatie/typescript-transformer
composer require --dev barryvdh/laravel-ide-helper
```

### 1.2 Publish vendor configs / migrations

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
# (laravel-permission publishes config + migrations)

php artisan vendor:publish --provider="Spatie\LaravelData\LaravelDataServiceProvider"
# (optional, if you want config published)

php artisan typescript:install
# spatie/typescript-transformer provides an install command

php artisan migrate
```

---

## 2) Add agent guideline docs in `./.ai/guidelines`

Create these files:

### 2.1 `./.ai/guidelines/spatie-laravel-permission.md`

```md
# Spatie Laravel Permission usage (project standard)

## Goal
We use Spatie Permission for admin authorization: roles, permissions, and permission checks in:
- Controllers (authorize/can middleware)
- Inertia shared props (to drive nav visibility)
- Vue components (disable/hide UI by permission)
- Pest tests

## Naming convention
Permissions use a strict "group.action" format:
- users.view, users.create, users.update, users.delete, users.assignRoles
- roles.view, roles.create, roles.update, roles.delete, roles.assignPermissions
- permissions.view, permissions.create, permissions.update, permissions.delete

Additionally, each Permission row has a `group` column:
- group: users | roles | permissions
- name: group.action (string)

## Server-side rules
- All admin routes live under /admin
- Every route has explicit `can:` middleware
- Controllers also call `$this->authorize(...)` as defense in depth
- User model uses `HasRoles`

## Client-side rules
- Nav items only render if the user has the required permission
- Forms are disabled if lacking create/update permissions
- Submit handlers should be blocked if not allowed
- Always use the server as source of truth (UI checks are convenience)

## Boost integration
When laravel/boost generates models/controllers/pages:
- Ensure controllers include `->middleware('can:...')` at the route level
- Ensure tests exist for each permission gate
- Ensure seeders create all permissions + super-admin role
```

### 2.2 `./.ai/guidelines/spatie-laravel-data.md`

```md
# Spatie Laravel Data usage (project standard)

## Goal
Replace scattered request arrays with typed Data objects:
- Create/update payloads (UserUpsertData, RoleUpsertData, PermissionUpsertData)
- View models for Inertia pages (UserListData etc.)

## Rules
- Prefer Data objects over ad-hoc arrays
- Use `Data::from($request)` or `Data::validateAndCreate($request)` patterns when possible
- Keep Data objects immutable (public readonly where possible)
- Validation rules live in the Data object when practical
- Convert Eloquent models to Data for Inertia props, not vice versa

## Boost integration
When boost generates:
- Replace controller `$request->validate(...)` with Data validation
- Return `Data::collection(...)` when sending lists to the UI
```

### 2.3 `./.ai/guidelines/spatie-typescript-transformer.md`

```md
# Spatie TypeScript Transformer usage (project standard)

## Goal
Generate TS types from PHP Data objects and enums.

## Rules
- Annotate Data classes meant for the frontend
- Output goes to: resources/js/types/generated.d.ts (or similar)
- Add an npm script and a composer script to regenerate consistently

## Typical flow
1. Add/modify PHP Data class
2. Run generation command
3. Commit updated TS output

## Boost integration
When boost generates Data objects:
- Ensure they are annotated for TS generation
- Ensure the output file is updated and committed
```

### 2.4 `./.ai/guidelines/barryvdh-ide-helper.md`

```md
# Laravel IDE Helper usage (project standard)

## Goal
Keep IDE autocompletion accurate for facades, models, and macros.

## Rules
- Never run in production
- Prefer a composer script:
  - ide-helper:generate
  - ide-helper:models --nowrite
- Commit strategy:
  - ide-helper.php can be committed or ignored (team choice)
  - model helpers typically NOT committed in modern workflows unless required
```

---

## 3) Install + configure Spatie Permission with a `group` column on permissions

### 3.1 Update `User` model to use roles

`app/Models/User.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

final class User extends Authenticatable
{
    use HasRoles;

    // existing starter-kit traits/properties...
}
```

### 3.2 Add custom Permission model (with `group`)

Create `app/Models/Permission.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

final class Permission extends SpatiePermission
{
    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'guard_name',
        'group',
    ];
}
```

### 3.3 Tell Spatie to use your model

Edit `config/permission.php`:

```php
'models' => [
    'permission' => App\Models\Permission::class,
    'role' => Spatie\Permission\Models\Role::class,
],
```

### 3.4 Migration: add `group` column to permissions

```bash
php artisan make:migration add_group_to_permissions_table --table=permissions
```

`database/migrations/xxxx_xx_xx_add_group_to_permissions_table.php`

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table): void {
            $table->string('group')->default('misc')->index();
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table): void {
            $table->dropColumn('group');
        });
    }
};
```

Run it:

```bash
php artisan migrate
```

---

## 4) Seed “admin ACL” (roles + permissions)

### 4.1 Enum for permission names

`app/Enums/AdminPermission.php`

```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum AdminPermission: string
{
    // Users
    case UsersView = 'users.view';
    case UsersCreate = 'users.create';
    case UsersUpdate = 'users.update';
    case UsersDelete = 'users.delete';
    case UsersAssignRoles = 'users.assignRoles';

    // Roles
    case RolesView = 'roles.view';
    case RolesCreate = 'roles.create';
    case RolesUpdate = 'roles.update';
    case RolesDelete = 'roles.delete';
    case RolesAssignPermissions = 'roles.assignPermissions';

    // Permissions
    case PermissionsView = 'permissions.view';
    case PermissionsCreate = 'permissions.create';
    case PermissionsUpdate = 'permissions.update';
    case PermissionsDelete = 'permissions.delete';

    public function group(): string
    {
        return match ($this) {
            self::UsersView,
            self::UsersCreate,
            self::UsersUpdate,
            self::UsersDelete,
            self::UsersAssignRoles => 'users',

            self::RolesView,
            self::RolesCreate,
            self::RolesUpdate,
            self::RolesDelete,
            self::RolesAssignPermissions => 'roles',

            self::PermissionsView,
            self::PermissionsCreate,
            self::PermissionsUpdate,
            self::PermissionsDelete => 'permissions',
        };
    }
}
```

### 4.2 Seeder

```bash
php artisan make:seeder AdminAclSeeder
```

`database/seeders/AdminAclSeeder.php`

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AdminPermission;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

final class AdminAclSeeder extends Seeder
{
    public function run(): void
    {
        foreach (AdminPermission::cases() as $perm) {
            Permission::query()->firstOrCreate(
                ['name' => $perm->value, 'guard_name' => 'web'],
                ['group' => $perm->group()],
            );
        }

        $role = Role::query()->firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $role->syncPermissions(Permission::all());
    }
}
```

Call it from `DatabaseSeeder`:

```php
public function run(): void
{
    $this->call(AdminAclSeeder::class);
}
```

Run:

```bash
php artisan db:seed
```

---

## 5) Admin route group + make Dashboard the admin home

### 5.1 Routes

Edit `routes/web.php`:

```php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', fn () => Inertia::render('Admin/Dashboard'))
            ->name('dashboard');
    });

// Optional: keep /dashboard but redirect to /admin for logged-in users
Route::middleware(['auth', 'verified'])
    ->get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->name('dashboard');
```

### 5.2 Dashboard page

`resources/js/pages/Admin/Dashboard.vue`

```vue
<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
</script>

<template>
  <AdminLayout title="Admin Dashboard">
    <div class="space-y-4">
      <h1 class="text-2xl font-semibold">Admin</h1>
      <p class="text-sm opacity-80">Manage users, roles, and permissions.</p>
    </div>
  </AdminLayout>
</template>
```

---

## 6) Share permissions to Vue (nav + forms gating)

Edit `app/Http/Middleware/HandleInertiaRequests.php`:

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'roles' => $user?->getRoleNames(),
                'permissions' => $user?->getAllPermissions()->pluck('name')->values(),
            ],
        ]);
    }
}
```

---

## 7) Admin navigation gated by permission

### 7.1 Permission composable

`resources/js/composables/useAbility.ts`

```ts
import { usePage } from '@inertiajs/vue3'

export function useAbility() {
  const page = usePage()
  const permissions = (page.props.auth?.permissions ?? []) as string[]

  const can = (perm: string) => permissions.includes(perm)

  return { can }
}
```

### 7.2 AdminLayout

`resources/js/layouts/AdminLayout.vue`

```vue
<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useAbility } from '@/composables/useAbility'

defineProps<{ title: string }>()

const { can } = useAbility()

const nav = computed(() => [
  { label: 'Dashboard', href: route('admin.dashboard'), show: true },
  { label: 'Users', href: route('admin.users.index'), show: can('users.view') },
  { label: 'Roles', href: route('admin.roles.index'), show: can('roles.view') },
  { label: 'Permissions', href: route('admin.permissions.index'), show: can('permissions.view') },
])
</script>

<template>
  <div class="min-h-screen">
    <header class="px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <AppLogoIcon class="h-8 w-8" />
          <span class="font-semibold">Southeast Code</span>
        </div>
        <nav class="flex gap-4">
          <Link
            v-for="item in nav"
            :key="item.href"
            v-if="item.show"
            :href="item.href"
            class="text-sm opacity-90 hover:opacity-100"
          >
            {{ item.label }}
          </Link>
        </nav>
      </div>
    </header>

    <main class="px-6 py-6">
      <slot />
    </main>
  </div>
</template>
```

---

## 8) Users / Roles / Permissions routes

Add to the `/admin` route group:

```php
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\PermissionsController;

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');

        // Users
        Route::get('/users', [UsersController::class, 'index'])
            ->middleware('can:users.view')
            ->name('users.index');

        Route::get('/users/create', [UsersController::class, 'create'])
            ->middleware('can:users.create')
            ->name('users.create');

        Route::post('/users', [UsersController::class, 'store'])
            ->middleware('can:users.create')
            ->name('users.store');

        Route::get('/users/{user}/edit', [UsersController::class, 'edit'])
            ->middleware('can:users.update')
            ->name('users.edit');

        Route::put('/users/{user}', [UsersController::class, 'update'])
            ->middleware('can:users.update')
            ->name('users.update');

        Route::delete('/users/{user}', [UsersController::class, 'destroy'])
            ->middleware('can:users.delete')
            ->name('users.destroy');

        Route::put('/users/{user}/roles', [UsersController::class, 'syncRoles'])
            ->middleware('can:users.assignRoles')
            ->name('users.roles.sync');

        // Roles
        Route::get('/roles', [RolesController::class, 'index'])
            ->middleware('can:roles.view')
            ->name('roles.index');

        Route::get('/roles/create', [RolesController::class, 'create'])
            ->middleware('can:roles.create')
            ->name('roles.create');

        Route::post('/roles', [RolesController::class, 'store'])
            ->middleware('can:roles.create')
            ->name('roles.store');

        Route::get('/roles/{role}/edit', [RolesController::class, 'edit'])
            ->middleware('can:roles.update')
            ->name('roles.edit');

        Route::put('/roles/{role}', [RolesController::class, 'update'])
            ->middleware('can:roles.update')
            ->name('roles.update');

        Route::delete('/roles/{role}', [RolesController::class, 'destroy'])
            ->middleware('can:roles.delete')
            ->name('roles.destroy');

        Route::put('/roles/{role}/permissions', [RolesController::class, 'syncPermissions'])
            ->middleware('can:roles.assignPermissions')
            ->name('roles.permissions.sync');

        // Permissions
        Route::get('/permissions', [PermissionsController::class, 'index'])
            ->middleware('can:permissions.view')
            ->name('permissions.index');

        Route::get('/permissions/create', [PermissionsController::class, 'create'])
            ->middleware('can:permissions.create')
            ->name('permissions.create');

        Route::post('/permissions', [PermissionsController::class, 'store'])
            ->middleware('can:permissions.create')
            ->name('permissions.store');

        Route::get('/permissions/{permission}/edit', [PermissionsController::class, 'edit'])
            ->middleware('can:permissions.update')
            ->name('permissions.edit');

        Route::put('/permissions/{permission}', [PermissionsController::class, 'update'])
            ->middleware('can:permissions.update')
            ->name('permissions.update');

        Route::delete('/permissions/{permission}', [PermissionsController::class, 'destroy'])
            ->middleware('can:permissions.delete')
            ->name('permissions.destroy');
    });
```

---

## 9) Backend Data objects

### 9.1 `app/Data/Admin/UserUpsertData.php`

```php
<?php

declare(strict_types=1);

namespace App\Data\Admin;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

final class UserUpsertData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,

        #[Email, Max(255)]
        public string $email,

        #[Min(8)]
        public ?string $password,
    ) {}
}
```

### 9.2 `app/Data/Admin/RoleUpsertData.php`

```php
<?php

declare(strict_types=1);

namespace App\Data\Admin;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class RoleUpsertData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
    ) {}
}
```

### 9.3 `app/Data/Admin/PermissionUpsertData.php`

```php
<?php

declare(strict_types=1);

namespace App\Data\Admin;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class PermissionUpsertData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,

        #[In(['users', 'roles', 'permissions'])]
        public string $group,
    ) {}
}
```

---

## 10) UsersController

Create:

```bash
php artisan make:controller Admin/UsersController
```

`app/Http/Controllers/Admin/UsersController.php`

```php
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
        return Inertia::render('Admin/Users/Index', [
            'users' => User::query()
                ->select(['id', 'name', 'email', 'created_at'])
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
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
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->only(['id', 'name', 'email']),
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
            'userRoles' => $user->getRoleNames(),
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
}
```

---

## 11) RolesController + PermissionsController

Create:

```bash
php artisan make:controller Admin/RolesController
php artisan make:controller Admin/PermissionsController
```

### 11.1 `app/Http/Controllers/Admin/RolesController.php`

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Data\Admin\RoleUpsertData;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

final class RolesController
{
    public function index(): Response
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Roles/Create');
    }

    public function store(): RedirectResponse
    {
        $data = RoleUpsertData::validateAndCreate(request());

        $name = trim($data->name);

        request()->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')],
        ]);

        $role = Role::query()->create([
            'name' => $name,
            'guard_name' => 'web',
        ]);

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

        return Inertia::render('Admin/Roles/Edit', [
            'role' => $role->only(['id', 'name']),
            'permissionsByGroup' => $permissions,
            'rolePermissions' => $role->permissions()->pluck('name')->values(),
        ]);
    }

    public function update(Role $role): RedirectResponse
    {
        $data = RoleUpsertData::validateAndCreate(request());

        request()->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
        ]);

        $role->forceFill(['name' => trim($data->name)])->save();

        return back()->with('success', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted.');
    }

    public function syncPermissions(Role $role): RedirectResponse
    {
        $validated = request()->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', 'max:255'],
        ]);

        $permissionNames = $validated['permissions'] ?? [];

        $existing = Permission::query()
            ->whereIn('name', $permissionNames)
            ->pluck('name')
            ->all();

        $role->syncPermissions($existing);

        return back()->with('success', 'Permissions updated.');
    }
}
```

### 11.2 `app/Http/Controllers/Admin/PermissionsController.php`

```php
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
```

---

## 12) MD3-ish UI layer (Tailwind 4 + reka-ui friendly)

### 12.1 Ripple directive

`resources/js/directives/ripple.ts`

```ts
import type { Directive } from 'vue'

export const ripple: Directive<HTMLElement, void> = {
  mounted(el) {
    el.style.position ||= 'relative'
    el.style.overflow = 'hidden'

    el.addEventListener('click', (e: MouseEvent) => {
      const rect = el.getBoundingClientRect()
      const size = Math.max(rect.width, rect.height)
      const x = e.clientX - rect.left - size / 2
      const y = e.clientY - rect.top - size / 2

      const span = document.createElement('span')
      span.className = 'md3-ripple'
      span.style.width = `${size}px`
      span.style.height = `${size}px`
      span.style.left = `${x}px`
      span.style.top = `${y}px`

      el.appendChild(span)
      span.addEventListener('animationend', () => span.remove())
    })
  },
}
```

Register it in `resources/js/app.ts`:

```ts
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { ripple } from '@/directives/ripple'

createInertiaApp({
  setup({ el, App, props, plugin }) {
    const vue = createApp({ render: () => h(App, props) })
    vue.use(plugin)
    vue.directive('ripple', ripple)
    vue.mount(el)
  },
})
```

### 12.2 Floating label input

`resources/js/components/md3/TextField.vue`

```vue
<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
  modelValue: string
  label: string
  type?: string
  variant?: 'filled' | 'outlined'
  state?: 'default' | 'error' | 'success'
  disabled?: boolean
  message?: string
}>(), {
  type: 'text',
  variant: 'outlined',
  state: 'default',
  disabled: false,
})

const emit = defineEmits<{ (e: 'update:modelValue', v: string): void }>()

const rootClass = computed(() => [
  'md3-field',
  `md3-field--${props.variant}`,
  `md3-field--${props.state}`,
  props.disabled ? 'opacity-60 pointer-events-none' : '',
].join(' '))
</script>

<template>
  <div class="space-y-1">
    <div :class="rootClass">
      <input
        :type="type"
        class="md3-field__input"
        :disabled="disabled"
        :value="modelValue"
        placeholder=" "
        @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      />
      <label class="md3-field__label">{{ label }}</label>
    </div>

    <p v-if="message" class="text-xs md3-field__message">{{ message }}</p>
  </div>
</template>
```

### 12.3 Button + Card components

`resources/js/components/md3/Button.vue`

```vue
<script setup lang="ts">
withDefaults(defineProps<{
  as?: 'button' | 'a'
  variant?: 'filled' | 'tonal' | 'outlined' | 'text'
  disabled?: boolean
  type?: 'button' | 'submit'
}>(), {
  as: 'button',
  variant: 'filled',
  disabled: false,
  type: 'button',
})
</script>

<template>
  <component
    :is="as"
    v-ripple
    :type="as === 'button' ? type : undefined"
    :disabled="as === 'button' ? disabled : undefined"
    :class="['md3-btn', `md3-btn--${variant}`, disabled ? 'opacity-60 pointer-events-none' : '']"
  >
    <slot />
  </component>
</template>
```

`resources/js/components/md3/Card.vue`

```vue
<script setup lang="ts">
withDefaults(defineProps<{
  elevation?: 0 | 1 | 2 | 3
}>(), { elevation: 1 })
</script>

<template>
  <div :class="['md3-card', `md3-card--e${elevation}`]">
    <slot />
  </div>
</template>
```

---

## 13) Update `resources/css/app.css` (Southeast Code light/dark + MD3 tokens + autofill overrides)

Replace/merge into `resources/css/app.css`:

```css
@import "tailwindcss";

/* -------------------------
   Southeast Code theme tokens
   ------------------------- */
:root {
  /* Core */
  --sc-base: #1e293b;
  --sc-primary: #3abdf7;
  --sc-secondary: #818cf8;
  --sc-accent: #f471b5;

  /* Light surface system */
  --md-bg: #f8fafc;
  --md-surface: #ffffff;
  --md-surface-2: #f1f5f9;
  --md-text: #0f172a;
  --md-outline: rgba(15, 23, 42, 0.18);

  /* Semantic */
  --md-error: #ef4444;
  --md-success: #22c55e;

  /* MD3-ish shape/elevation */
  --md-radius: 16px;
  --md-radius-sm: 12px;
  --md-e1: 0 1px 2px rgba(2, 6, 23, 0.08);
  --md-e2: 0 6px 14px rgba(2, 6, 23, 0.12);
  --md-e3: 0 14px 28px rgba(2, 6, 23, 0.16);

  /* Component tokens */
  --md-field-bg: rgba(30, 41, 59, 0.04);
  --md-field-text: var(--md-text);
  --md-field-label: rgba(15, 23, 42, 0.65);
  --md-field-focus: var(--sc-primary);

  --md-ripple: rgba(58, 189, 247, 0.22);
}

.dark {
  --md-bg: #0b1220;
  --md-surface: #0f172a;
  --md-surface-2: #111c35;
  --md-text: #e2e8f0;
  --md-outline: rgba(226, 232, 240, 0.18);

  --md-field-bg: rgba(248, 250, 252, 0.06);
  --md-field-label: rgba(226, 232, 240, 0.72);
  --md-ripple: rgba(58, 189, 247, 0.18);
}

html, body {
  background: var(--md-bg);
  color: var(--md-text);
}

/* -------------------------
   Autofill override
   ------------------------- */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
textarea:-webkit-autofill {
  -webkit-text-fill-color: var(--md-field-text);
  -webkit-box-shadow: 0 0 0px 1000px var(--md-surface) inset;
  transition: background-color 99999s ease-in-out 0s;
}

.dark input:-webkit-autofill,
.dark textarea:-webkit-autofill {
  -webkit-box-shadow: 0 0 0px 1000px var(--md-surface) inset;
}

/* -------------------------
   MD3-ish components
   ------------------------- */
.md3-card {
  border-radius: var(--md-radius);
  background: var(--md-surface);
  padding: 16px;
  box-shadow: var(--md-e1);
}

.md3-card--e0 { box-shadow: none; }
.md3-card--e1 { box-shadow: var(--md-e1); }
.md3-card--e2 { box-shadow: var(--md-e2); }
.md3-card--e3 { box-shadow: var(--md-e3); }

.md3-btn {
  position: relative;
  border-radius: 999px;
  padding: 10px 14px;
  font-size: 14px;
  font-weight: 600;
  user-select: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.md3-btn--filled {
  background: var(--sc-primary);
  color: #08111f;
}

.md3-btn--tonal {
  background: rgba(58, 189, 247, 0.14);
  color: var(--md-text);
}

.md3-btn--outlined {
  border: 1px solid var(--md-outline);
  color: var(--md-text);
  background: transparent;
}

.md3-btn--text {
  color: var(--md-text);
  background: transparent;
}

/* Ripple element */
.md3-ripple {
  position: absolute;
  border-radius: 999px;
  transform: scale(0);
  background: var(--md-ripple);
  animation: md3-ripple 520ms ease-out;
  pointer-events: none;
}

@keyframes md3-ripple {
  to {
    transform: scale(1);
    opacity: 0;
  }
}

/* Floating label field */
.md3-field {
  position: relative;
  border-radius: var(--md-radius-sm);
}

.md3-field__input {
  width: 100%;
  border-radius: var(--md-radius-sm);
  padding: 18px 14px 10px;
  outline: none;
  background: var(--md-field-bg);
  color: var(--md-field-text);
  border: 1px solid transparent;
}

.md3-field--outlined .md3-field__input {
  background: transparent;
  border: 1px solid var(--md-outline);
}

.md3-field__label {
  position: absolute;
  left: 14px;
  top: 14px;
  font-size: 14px;
  color: var(--md-field-label);
  transform-origin: left top;
  transition: transform 160ms ease, top 160ms ease, color 160ms ease;
  pointer-events: none;
}

/* Float on focus or when there is content (placeholder is " ") */
.md3-field__input:focus + .md3-field__label,
.md3-field__input:not(:placeholder-shown) + .md3-field__label {
  top: 6px;
  transform: scale(0.85);
}

.md3-field__input:focus {
  border-color: var(--md-field-focus);
}

.md3-field--error .md3-field__input { border-color: var(--md-error); }
.md3-field--error .md3-field__label { color: var(--md-error); }
.md3-field--success .md3-field__input { border-color: var(--md-success); }
.md3-field--success .md3-field__label { color: var(--md-success); }

.md3-field__message {
  opacity: 0.8;
}
```

---

## 14) Update `AppLogoIcon` to a Southeast Code mark

`resources/js/components/AppLogoIcon.vue`

```vue
<script setup lang="ts">
defineProps<{ class?: string }>()
</script>

<template>
  <svg
    :class="class"
    viewBox="0 0 64 64"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-label="Southeast Code"
  >
    <rect x="6" y="6" width="52" height="52" rx="14" fill="var(--md-surface)" />
    <path
      d="M23 22c0-3 2.4-5 6-5h10c3.6 0 6 2 6 5s-2.4 5-6 5H29c-3.6 0-6 2-6 5s2.4 5 6 5h10"
      stroke="var(--sc-primary)"
      stroke-width="4"
      stroke-linecap="round"
    />
    <path
      d="M24 38l-6-6 6-6"
      stroke="var(--sc-secondary)"
      stroke-width="3.5"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      d="M40 26l6 6-6 6"
      stroke="var(--sc-accent)"
      stroke-width="3.5"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
  </svg>
</template>
```

---

## 15) Vue pages

Create these files:

- `resources/js/pages/Admin/Users/Index.vue`
- `resources/js/pages/Admin/Users/Create.vue`
- `resources/js/pages/Admin/Users/Edit.vue`
- `resources/js/pages/Admin/Roles/Index.vue`
- `resources/js/pages/Admin/Roles/Create.vue`
- `resources/js/pages/Admin/Roles/Edit.vue`
- `resources/js/pages/Admin/Permissions/Index.vue`
- `resources/js/pages/Admin/Permissions/Create.vue`
- `resources/js/pages/Admin/Permissions/Edit.vue`

### 15.1 Users Index

`resources/js/pages/Admin/Users/Index.vue`

```vue
<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useAbility } from '@/composables/useAbility'

defineProps<{ users: any }>()

const { can } = useAbility()
const page = usePage()
</script>

<template>
  <AdminLayout title="Users">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Users</h1>

      <Link
        v-if="can('users.create')"
        :href="route('admin.users.create')"
        class="md3-btn md3-btn--filled"
        v-ripple
      >
        New user
      </Link>
    </div>

    <div class="mt-6 md3-card">
      <table class="w-full text-sm">
        <thead class="opacity-70">
          <tr>
            <th class="p-3 text-left">Name</th>
            <th class="p-3 text-left">Email</th>
            <th class="p-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in users.data" :key="u.id" class="border-t border-black/5 dark:border-white/10">
            <td class="p-3">{{ u.name }}</td>
            <td class="p-3">{{ u.email }}</td>
            <td class="p-3 text-right">
              <Link
                v-if="can('users.update')"
                :href="route('admin.users.edit', u.id)"
                class="md3-btn md3-btn--text"
                v-ripple
              >
                Edit
              </Link>

              <form
                v-if="can('users.delete')"
                class="inline"
                method="post"
                :action="route('admin.users.destroy', u.id)"
              >
                <input type="hidden" name="_method" value="delete" />
                <input type="hidden" name="_token" :value="page.props.csrf_token" />
                <button type="submit" class="md3-btn md3-btn--text" v-ripple>
                  Delete
                </button>
              </form>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
```

### 15.2 Users Create

`resources/js/pages/Admin/Users/Create.vue`

```vue
<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/md3/Card.vue'
import Button from '@/components/md3/Button.vue'
import TextField from '@/components/md3/TextField.vue'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useAbility } from '@/composables/useAbility'

const props = defineProps<{
  roles: { id: number; name: string }[]
}>()

const { can } = useAbility()
const canCreate = computed(() => can('users.create'))

const form = useForm({
  name: '',
  email: '',
  password: '',
})

const submit = () => {
  if (!canCreate.value) return
  form.post(route('admin.users.store'))
}
</script>

<template>
  <AdminLayout title="Create User">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Create user</h1>
    </div>

    <Card class="mt-6" :elevation="1">
      <form class="space-y-4" @submit.prevent="submit">
        <TextField
          v-model="form.name"
          label="Name"
          :state="form.errors.name ? 'error' : 'default'"
          :message="form.errors.name"
          :disabled="!canCreate"
          variant="outlined"
        />

        <TextField
          v-model="form.email"
          label="Email"
          type="email"
          :state="form.errors.email ? 'error' : 'default'"
          :message="form.errors.email"
          :disabled="!canCreate"
          variant="outlined"
        />

        <TextField
          v-model="form.password"
          label="Password"
          type="password"
          :state="form.errors.password ? 'error' : 'default'"
          :message="form.errors.password"
          :disabled="!canCreate"
          variant="outlined"
        />

        <div class="flex justify-end">
          <Button variant="filled" type="submit" :disabled="!canCreate || form.processing">
            Create
          </Button>
        </div>
      </form>
    </Card>
  </AdminLayout>
</template>
```

### 15.3 Users Edit

`resources/js/pages/Admin/Users/Edit.vue`

```vue
<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/md3/Card.vue'
import Button from '@/components/md3/Button.vue'
import TextField from '@/components/md3/TextField.vue'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useAbility } from '@/composables/useAbility'

const props = defineProps<{
  user: { id: number; name: string; email: string }
  roles: { id: number; name: string }[]
  userRoles: string[]
}>()

const { can } = useAbility()

const canUpdate = computed(() => can('users.update'))
const canAssignRoles = computed(() => can('users.assignRoles'))
const canDelete = computed(() => can('users.delete'))

const userForm = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
})

const rolesForm = useForm({
  roles: [...props.userRoles],
})

const updateUser = () => {
  if (!canUpdate.value) return
  userForm.put(route('admin.users.update', props.user.id), { preserveScroll: true })
}

const syncRoles = () => {
  if (!canAssignRoles.value) return
  rolesForm.put(route('admin.users.roles.sync', props.user.id), { preserveScroll: true })
}

const destroyUser = () => {
  if (!canDelete.value) return
  if (!confirm('Delete this user? This is not reversible.')) return
  userForm.delete(route('admin.users.destroy', props.user.id))
}

const toggleRole = (roleName: string) => {
  const idx = rolesForm.roles.indexOf(roleName)
  if (idx >= 0) rolesForm.roles.splice(idx, 1)
  else rolesForm.roles.push(roleName)
}
</script>

<template>
  <AdminLayout title="Edit User">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Edit user</h1>

      <Button variant="text" :disabled="!canDelete" @click="destroyUser">
        Delete
      </Button>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
      <Card :elevation="1">
        <h2 class="text-lg font-semibold">Details</h2>

        <form class="mt-4 space-y-4" @submit.prevent="updateUser">
          <TextField
            v-model="userForm.name"
            label="Name"
            :state="userForm.errors.name ? 'error' : 'default'"
            :message="userForm.errors.name"
            :disabled="!canUpdate"
            variant="outlined"
          />

          <TextField
            v-model="userForm.email"
            label="Email"
            type="email"
            :state="userForm.errors.email ? 'error' : 'default'"
            :message="userForm.errors.email"
            :disabled="!canUpdate"
            variant="outlined"
          />

          <TextField
            v-model="userForm.password"
            label="New password (optional)"
            type="password"
            :state="userForm.errors.password ? 'error' : 'default'"
            :message="userForm.errors.password"
            :disabled="!canUpdate"
            variant="filled"
          />

          <div class="flex justify-end">
            <Button variant="filled" type="submit" :disabled="!canUpdate || userForm.processing">
              Save
            </Button>
          </div>
        </form>
      </Card>

      <Card :elevation="1">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold">Roles</h2>
          <Button variant="tonal" :disabled="!canAssignRoles || rolesForm.processing" @click="syncRoles">
            Update roles
          </Button>
        </div>

        <div class="mt-4 space-y-2">
          <label
            v-for="r in roles"
            :key="r.id"
            class="flex items-center gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10"
            :class="!canAssignRoles ? 'opacity-60' : ''"
          >
            <input
              type="checkbox"
              class="h-4 w-4"
              :disabled="!canAssignRoles"
              :checked="rolesForm.roles.includes(r.name)"
              @change="toggleRole(r.name)"
            />
            <span class="text-sm">{{ r.name }}</span>
          </label>
        </div>

        <p v-if="rolesForm.errors.roles" class="mt-2 text-xs opacity-80">
          {{ rolesForm.errors.roles }}
        </p>
      </Card>
    </div>
  </AdminLayout>
</template>
```

### 15.4 Roles Index/Create/Edit

`resources/js/pages/Admin/Roles/Index.vue`

```vue
<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/md3/Card.vue'
import Button from '@/components/md3/Button.vue'
import { Link } from '@inertiajs/vue3'
import { useAbility } from '@/composables/useAbility'
import { computed } from 'vue'

const props = defineProps<{
  roles: { id: number; name: string }[]
}>()

const { can } = useAbility()
const canCreate = computed(() => can('roles.create'))
const canUpdate = computed(() => can('roles.update'))
</script>

<template>
  <AdminLayout title="Roles">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Roles</h1>

      <Link v-if="canCreate" :href="route('admin.roles.create')">
        <Button variant="filled">New role</Button>
      </Link>
    </div>

    <Card class="mt-6" :elevation="1">
      <div class="space-y-2">
        <div
          v-for="r in roles"
          :key="r.id"
          class="flex items-center justify-between rounded-xl border border-black/5 p-3 dark:border-white/10"
        >
          <div class="text-sm font-medium">{{ r.name }}</div>

          <Link v-if="canUpdate" :href="route('admin.roles.edit', r.id)">
            <Button variant="text">Edit</Button>
          </Link>
        </div>
      </div>
    </Card>
  </AdminLayout>
</template>
```

`resources/js/pages/Admin/Roles/Create.vue`

```vue
<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/md3/Card.vue'
import Button from '@/components/md3/Button.vue'
import TextField from '@/components/md3/TextField.vue'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useAbility } from '@/composables/useAbility'

const { can } = useAbility()
const canCreate = computed(() => can('roles.create'))

const form = useForm({ name: '' })

const submit = () => {
  if (!canCreate.value) return
  form.post(route('admin.roles.store'))
}
</script>

<template>
  <AdminLayout title="Create Role">
    <h1 class="text-2xl font-semibold">Create role</h1>

    <Card class="mt-6" :elevation="1">
      <form class="space-y-4" @submit.prevent="submit">
        <TextField
          v-model="form.name"
          label="Role name"
          :state="form.errors.name ? 'error' : 'default'"
          :message="form.errors.name"
          :disabled="!canCreate"
          variant="outlined"
        />

        <div class="flex justify-end">
          <Button variant="filled" type="submit" :disabled="!canCreate || form.processing">
            Create
          </Button>
        </div>
      </form>
    </Card>
  </AdminLayout>
</template>
```

`resources/js/pages/Admin/Roles/Edit.vue`

```vue
<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/md3/Card.vue'
import Button from '@/components/md3/Button.vue'
import TextField from '@/components/md3/TextField.vue'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useAbility } from '@/composables/useAbility'

const props = defineProps<{
  role: { id: number; name: string }
  permissionsByGroup: Record<string, { id: number; name: string; group: string }[]>
  rolePermissions: string[]
}>()

const { can } = useAbility()
const canUpdate = computed(() => can('roles.update'))
const canDelete = computed(() => can('roles.delete'))
const canAssign = computed(() => can('roles.assignPermissions'))

const roleForm = useForm({ name: props.role.name })
const permsForm = useForm({ permissions: [...props.rolePermissions] })

const updateRole = () => {
  if (!canUpdate.value) return
  roleForm.put(route('admin.roles.update', props.role.id), { preserveScroll: true })
}

const syncPermissions = () => {
  if (!canAssign.value) return
  permsForm.put(route('admin.roles.permissions.sync', props.role.id), { preserveScroll: true })
}

const destroyRole = () => {
  if (!canDelete.value) return
  if (!confirm('Delete this role?')) return
  roleForm.delete(route('admin.roles.destroy', props.role.id))
}

const togglePermission = (name: string) => {
  const idx = permsForm.permissions.indexOf(name)
  if (idx >= 0) permsForm.permissions.splice(idx, 1)
  else permsForm.permissions.push(name)
}
</script>

<template>
  <AdminLayout title="Edit Role">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Edit role</h1>
      <Button variant="text" :disabled="!canDelete" @click="destroyRole">Delete</Button>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
      <Card :elevation="1">
        <h2 class="text-lg font-semibold">Details</h2>

        <form class="mt-4 space-y-4" @submit.prevent="updateRole">
          <TextField
            v-model="roleForm.name"
            label="Role name"
            :state="roleForm.errors.name ? 'error' : 'default'"
            :message="roleForm.errors.name"
            :disabled="!canUpdate"
            variant="outlined"
          />

          <div class="flex justify-end">
            <Button variant="filled" type="submit" :disabled="!canUpdate || roleForm.processing">
              Save
            </Button>
          </div>
        </form>
      </Card>

      <Card :elevation="1">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold">Permissions</h2>
          <Button variant="tonal" :disabled="!canAssign || permsForm.processing" @click="syncPermissions">
            Update permissions
          </Button>
        </div>

        <div class="mt-4 space-y-5">
          <div v-for="(items, group) in permissionsByGroup" :key="group" class="space-y-2">
            <div class="text-sm font-semibold capitalize">{{ group }}</div>

            <label
              v-for="p in items"
              :key="p.id"
              class="flex items-center gap-3 rounded-xl border border-black/5 p-3 dark:border-white/10"
              :class="!canAssign ? 'opacity-60' : ''"
            >
              <input
                type="checkbox"
                class="h-4 w-4"
                :disabled="!canAssign"
                :checked="permsForm.permissions.includes(p.name)"
                @change="togglePermission(p.name)"
              />
              <span class="text-sm">{{ p.name }}</span>
            </label>
          </div>
        </div>
      </Card>
    </div>
  </AdminLayout>
</template>
```

### 15.5 Permissions Index/Create/Edit

`resources/js/pages/Admin/Permissions/Index.vue`

```vue
<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/md3/Card.vue'
import Button from '@/components/md3/Button.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useAbility } from '@/composables/useAbility'
import { computed } from 'vue'

const props = defineProps<{
  permissionsByGroup: Record<string, { id: number; name: string; group: string }[]>
}>()

const { can } = useAbility()
const canCreate = computed(() => can('permissions.create'))
const canUpdate = computed(() => can('permissions.update'))
const canDelete = computed(() => can('permissions.delete'))

const del = useForm({})

const destroyPermission = (id: number) => {
  if (!canDelete.value) return
  if (!confirm('Delete this permission?')) return
  del.delete(route('admin.permissions.destroy', id))
}
</script>

<template>
  <AdminLayout title="Permissions">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Permissions</h1>

      <Link v-if="canCreate" :href="route('admin.permissions.create')">
        <Button variant="filled">New permission</Button>
      </Link>
    </div>

    <div class="mt-6 space-y-6">
      <Card v-for="(items, group) in permissionsByGroup" :key="group" :elevation="1">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold capitalize">{{ group }}</h2>
        </div>

        <div class="mt-4 space-y-2">
          <div
            v-for="p in items"
            :key="p.id"
            class="flex items-center justify-between rounded-xl border border-black/5 p-3 dark:border-white/10"
          >
            <div class="text-sm font-medium">{{ p.name }}</div>

            <div class="flex items-center gap-2">
              <Link v-if="canUpdate" :href="route('admin.permissions.edit', p.id)">
                <Button variant="text">Edit</Button>
              </Link>

              <Button v-if="canDelete" variant="text" @click="destroyPermission(p.id)">
                Delete
              </Button>
            </div>
          </div>
        </div>
      </Card>
    </div>
  </AdminLayout>
</template>
```

`resources/js/pages/Admin/Permissions/Create.vue`

```vue
<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/md3/Card.vue'
import Button from '@/components/md3/Button.vue'
import TextField from '@/components/md3/TextField.vue'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useAbility } from '@/composables/useAbility'

const { can } = useAbility()
const canCreate = computed(() => can('permissions.create'))

const form = useForm({
  name: '',
  group: 'users',
})

const submit = () => {
  if (!canCreate.value) return
  form.post(route('admin.permissions.store'))
}
</script>

<template>
  <AdminLayout title="Create Permission">
    <h1 class="text-2xl font-semibold">Create permission</h1>

    <Card class="mt-6" :elevation="1">
      <form class="space-y-4" @submit.prevent="submit">
        <TextField
          v-model="form.name"
          label="Permission name (e.g. users.view)"
          :state="form.errors.name ? 'error' : 'default'"
          :message="form.errors.name"
          :disabled="!canCreate"
          variant="outlined"
        />

        <div class="space-y-1">
          <div class="text-sm font-medium opacity-80">Group</div>
          <select
            v-model="form.group"
            class="w-full rounded-xl border border-black/10 bg-[var(--md-surface)] p-3 text-sm dark:border-white/10"
            :disabled="!canCreate"
          >
            <option value="users">users</option>
            <option value="roles">roles</option>
            <option value="permissions">permissions</option>
          </select>
          <p v-if="form.errors.group" class="text-xs opacity-80">{{ form.errors.group }}</p>
        </div>

        <div class="flex justify-end">
          <Button variant="filled" type="submit" :disabled="!canCreate || form.processing">
            Create
          </Button>
        </div>
      </form>
    </Card>
  </AdminLayout>
</template>
```

`resources/js/pages/Admin/Permissions/Edit.vue`

```vue
<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/md3/Card.vue'
import Button from '@/components/md3/Button.vue'
import TextField from '@/components/md3/TextField.vue'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useAbility } from '@/composables/useAbility'

const props = defineProps<{
  permission: { id: number; name: string; group: string }
}>()

const { can } = useAbility()
const canUpdate = computed(() => can('permissions.update'))
const canDelete = computed(() => can('permissions.delete'))

const form = useForm({
  name: props.permission.name,
  group: props.permission.group,
})

const updatePermission = () => {
  if (!canUpdate.value) return
  form.put(route('admin.permissions.update', props.permission.id))
}

const destroyPermission = () => {
  if (!canDelete.value) return
  if (!confirm('Delete this permission?')) return
  form.delete(route('admin.permissions.destroy', props.permission.id))
}
</script>

<template>
  <AdminLayout title="Edit Permission">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Edit permission</h1>
      <Button variant="text" :disabled="!canDelete" @click="destroyPermission">Delete</Button>
    </div>

    <Card class="mt-6" :elevation="1">
      <form class="space-y-4" @submit.prevent="updatePermission">
        <TextField
          v-model="form.name"
          label="Permission name"
          :state="form.errors.name ? 'error' : 'default'"
          :message="form.errors.name"
          :disabled="!canUpdate"
          variant="outlined"
        />

        <div class="space-y-1">
          <div class="text-sm font-medium opacity-80">Group</div>
          <select
            v-model="form.group"
            class="w-full rounded-xl border border-black/10 bg-[var(--md-surface)] p-3 text-sm dark:border-white/10"
            :disabled="!canUpdate"
          >
            <option value="users">users</option>
            <option value="roles">roles</option>
            <option value="permissions">permissions</option>
          </select>
          <p v-if="form.errors.group" class="text-xs opacity-80">{{ form.errors.group }}</p>
        </div>

        <div class="flex justify-end">
          <Button variant="filled" type="submit" :disabled="!canUpdate || form.processing">
            Save
          </Button>
        </div>
      </form>
    </Card>
  </AdminLayout>
</template>
```

---

## 16) Pest tests

### 16.1 Admin helper

`tests/Support/AdminUserFactory.php`

```php
<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Models\User;
use Spatie\Permission\Models\Role;

final class AdminUserFactory
{
    public static function superAdmin(): User
    {
        $user = User::factory()->create();
        $role = Role::query()->firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $user->assignRole($role);

        return $user;
    }
}
```

### 16.2 Seed ACL in tests

In `tests/Pest.php`:

```php
use Database\Seeders\AdminAclSeeder;

uses(Tests\TestCase::class)->in('Feature');

beforeEach(function () {
    $this->seed(AdminAclSeeder::class);
});
```

### 16.3 Users permission gating example

`tests/Feature/Admin/UsersPermissionsTest.php`

```php
<?php

use App\Models\User;
use Tests\Support\AdminUserFactory;

it('blocks users index without permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

it('allows super-admin to view users index', function () {
    $admin = AdminUserFactory::superAdmin();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk();
});
```

### 16.4 Helper to create a user with specific permissions

`tests/Support/UserPermissions.php`

```php
<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Models\User;

final class UserPermissions
{
    /**
     * @param array<int, string> $permissions
     */
    public static function make(array $permissions): User
    {
        $user = User::factory()->create();

        foreach ($permissions as $permission) {
            $user->givePermissionTo($permission);
        }

        return $user;
    }
}
```

### 16.5 Roles gates + CRUD

`tests/Feature/Admin/RolesTest.php`

```php
<?php

use Spatie\Permission\Models\Role;
use Tests\Support\UserPermissions;

it('blocks roles index without roles.view', function () {
    $user = UserPermissions::make([]);
    $this->actingAs($user)
        ->get(route('admin.roles.index'))
        ->assertForbidden();
});

it('shows roles index with roles.view', function () {
    $user = UserPermissions::make(['roles.view']);
    $this->actingAs($user)
        ->get(route('admin.roles.index'))
        ->assertOk();
});

it('creates role with roles.create', function () {
    $user = UserPermissions::make(['roles.create']);

    $this->actingAs($user)
        ->post(route('admin.roles.store'), ['name' => 'manager'])
        ->assertRedirect();

    expect(Role::query()->where('name', 'manager')->exists())->toBeTrue();
});

it('denies role creation without roles.create', function () {
    $user = UserPermissions::make([]);

    $this->actingAs($user)
        ->post(route('admin.roles.store'), ['name' => 'manager'])
        ->assertForbidden();
});
```

### 16.6 Role permission syncing

`tests/Feature/Admin/RolePermissionsSyncTest.php`

```php
<?php

use App\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\Support\UserPermissions;

it('syncs permissions only with roles.assignPermissions', function () {
    $role = Role::query()->create(['name' => 'manager', 'guard_name' => 'web']);

    $p1 = Permission::query()->create(['name' => 'users.view', 'group' => 'users', 'guard_name' => 'web']);
    $p2 = Permission::query()->create(['name' => 'users.create', 'group' => 'users', 'guard_name' => 'web']);

    $user = UserPermissions::make(['roles.assignPermissions']);

    $this->actingAs($user)
        ->put(route('admin.roles.permissions.sync', $role), [
            'permissions' => [$p1->name, $p2->name],
        ])
        ->assertRedirect();

    expect($role->fresh()->hasPermissionTo($p1->name))->toBeTrue();
    expect($role->fresh()->hasPermissionTo($p2->name))->toBeTrue();
});

it('blocks permission syncing without roles.assignPermissions', function () {
    $role = Role::query()->create(['name' => 'manager', 'guard_name' => 'web']);
    $user = UserPermissions::make([]);

    $this->actingAs($user)
        ->put(route('admin.roles.permissions.sync', $role), [
            'permissions' => ['users.view'],
        ])
        ->assertForbidden();
});
```

### 16.7 Permissions gates + CRUD

`tests/Feature/Admin/PermissionsTest.php`

```php
<?php

use App\Models\Permission;
use Tests\Support\UserPermissions;

it('blocks permissions index without permissions.view', function () {
    $user = UserPermissions::make([]);
    $this->actingAs($user)
        ->get(route('admin.permissions.index'))
        ->assertForbidden();
});

it('shows permissions index with permissions.view', function () {
    $user = UserPermissions::make(['permissions.view']);
    $this->actingAs($user)
        ->get(route('admin.permissions.index'))
        ->assertOk();
});

it('creates a permission with permissions.create', function () {
    $user = UserPermissions::make(['permissions.create']);

    $this->actingAs($user)
        ->post(route('admin.permissions.store'), ['name' => 'users.export', 'group' => 'users'])
        ->assertRedirect();

    expect(Permission::query()->where('name', 'users.export')->exists())->toBeTrue();
});

it('blocks permission creation without permissions.create', function () {
    $user = UserPermissions::make([]);

    $this->actingAs($user)
        ->post(route('admin.permissions.store'), ['name' => 'users.export', 'group' => 'users'])
        ->assertForbidden();
});
```

---

## 17) TypeScript generation scripts

Example script wiring:

`composer.json`:

```json
{
  "scripts": {
    "types:generate": [
      "php artisan typescript:transform"
    ],
    "ide:generate": [
      "php artisan ide-helper:generate",
      "php artisan ide-helper:models --nowrite"
    ]
  }
}
```

`package.json`:

```json
{
  "scripts": {
    "types:generate": "composer types:generate"
  }
}
```

Run:

```bash
composer types:generate
```

---

## 18) Final sanity checklist

```bash
php artisan migrate:fresh --seed
php artisan test
npm run build
```

Manual checks:
- `/admin` loads dashboard
- Nav only shows links you can access
- 403 when lacking permission for routes
- Buttons hidden/disabled when lacking permission
- Floating labels, ripple effect, autofill overrides work
- Light/dark theme toggling works (`.dark` class)

---

## 19) “laravel new --using …” readiness

### 19.1 Publish as your own template repo
- Create `SoutheastCode/laravel-vue-admin-starter`
- Push this work as main branch (or tag releases)

### 19.2 Installer-friendly composer script
Example:

```json
{
  "scripts": {
    "post-create-project-cmd": [
      "@php -r \"file_exists('.env') || copy('.env.example', '.env');\"",
      "@php artisan key:generate --ansi"
    ]
  }
}
```

---

## 20) Run everything

```bash
php artisan migrate:fresh --seed
php artisan test
npm run build
```
