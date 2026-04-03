<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Modules\Permissions\Models\Permission;
use App\Modules\Roles\Models\Role;
use App\Modules\Users\Actions\CreateUser;
use App\Modules\Users\DTOs\CreateUserData;
use App\Modules\Users\Models\User;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Validation\Rule;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

final class CreateUserCommand extends BaseInteractiveCreateCommand
{
    private const string SUPER_ADMIN_ROLE = 'super-admin';

    protected $signature = 'create:user';

    protected $description = 'Interactively create a user via the CreateUser action.';

    public function handle(): int
    {
        intro('Create an admin user');

        $name = text(
            label: 'Name',
            placeholder: 'Jane Doe',
            validate: fn (string $value): ?string => $this->validationMessage(
                ['name' => $value],
                ['name' => ['required', 'string', 'max:255']],
                'name',
            ),
        );

        $email = text(
            label: 'Email address',
            placeholder: 'jane@example.com',
            validate: fn (string $value): ?string => $this->validationMessage(
                ['email' => $value],
                ['email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->where(
                        fn (QueryBuilder $query) => $query->whereNull('deleted_at'),
                    ),
                ]],
                'email',
                ['email.unique' => 'A user with this email address already exists.'],
            ),
        );

        $plainPassword = password(
            label: 'Password',
            validate: fn (string $value): ?string => $this->validationMessage(
                ['password' => $value],
                ['password' => ['required', 'string', 'min:8']],
                'password',
            ),
        );

        password(
            label: 'Confirm password',
            validate: fn (string $value): ?string => $value !== $plainPassword
                ? 'Password confirmation must match.'
                : null,
        );

        $shouldDesignateSuperAdmin = false;

        if (! $this->hasActiveSuperAdminUser()) {
            $shouldDesignateSuperAdmin = confirm(
                label: 'No active super-admin users exist. Should this user be the designated super-admin?',
                default: true,
            );
        }

        $summaryRows = [
            ['Name', $name],
            ['Email', $email],
        ];

        if ($shouldDesignateSuperAdmin) {
            $summaryRows[] = ['Role', self::SUPER_ADMIN_ROLE];
        }

        $this->table(['Field', 'Value'], $summaryRows);

        if (! confirm(label: 'Create this user?', default: true)) {
            warning('User creation cancelled.');

            return SymfonyCommand::SUCCESS;
        }

        $user = CreateUser::handle(CreateUserData::fromInput([
            'name' => $name,
            'email' => $email,
            'password' => $plainPassword,
        ]));

        if ($shouldDesignateSuperAdmin) {
            $user->assignRole($this->ensureSuperAdminRole());
        }

        $createdRows = [
            ['ID', (string) $user->id],
            ['Name', $user->name],
            ['Email', $user->email],
        ];

        if ($shouldDesignateSuperAdmin) {
            $createdRows[] = ['Role', self::SUPER_ADMIN_ROLE];
        }

        $this->table(['Field', 'Value'], $createdRows);

        outro('User created.');

        return SymfonyCommand::SUCCESS;
    }

    private function hasActiveSuperAdminUser(): bool
    {
        return User::query()->role(self::SUPER_ADMIN_ROLE)->exists();
    }

    private function ensureSuperAdminRole(): Role
    {
        $role = Role::withTrashed()->firstOrNew([
            'name' => self::SUPER_ADMIN_ROLE,
            'guard_name' => 'web',
        ]);

        $role->forceFill([
            'name' => self::SUPER_ADMIN_ROLE,
            'guard_name' => 'web',
            'deleted_at' => null,
        ])->save();

        $role->syncPermissions(Permission::query()->get());

        return $role;
    }
}
