<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Modules\IAM\Actions\CreateUser;
use App\Modules\IAM\Actions\EnsureSuperAdminRole;
use App\Modules\IAM\Actions\HasActiveSuperAdminUser;
use App\Modules\IAM\DTOs\CreateUserData;
use App\Modules\Shared\Actions\PasswordValidationRules;
use App\Modules\Shared\Actions\UserIdentityValidationRules;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

final class CreateUserCommand extends BaseInteractiveCreateCommand
{
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
                ['name' => UserIdentityValidationRules::name()],
                'name',
            ),
        );

        $email = text(
            label: 'Email address',
            placeholder: 'jane@example.com',
            validate: fn (string $value): ?string => $this->validationMessage(
                ['email' => $value],
                ['email' => UserIdentityValidationRules::email()],
                'email',
                ['email.unique' => 'A user with this email address already exists.'],
            ),
        );

        $plainPassword = password(
            label: 'Password',
            validate: fn (string $value): ?string => $this->validationMessage(
                ['password' => $value],
                ['password' => $this->interactivePasswordRules()],
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

        if (! HasActiveSuperAdminUser::handle()) {
            $shouldDesignateSuperAdmin = confirm(
                label: 'No active super-admin users exist. Should this user be the designated super-admin?',
            );
        }

        $summaryRows = [
            ['Name', $name],
            ['Email', $email],
        ];

        if ($shouldDesignateSuperAdmin) {
            $summaryRows[] = ['Role', EnsureSuperAdminRole::name()];
        }

        $this->table(['Field', 'Value'], $summaryRows);

        if (! $this->confirmsOrCancels('Create this user?', 'User creation cancelled.')) {
            return SymfonyCommand::SUCCESS;
        }

        $user = CreateUser::handle(CreateUserData::fromInput([
            'name' => $name,
            'email' => $email,
            'password' => $plainPassword,
        ]));

        if ($shouldDesignateSuperAdmin) {
            $user->assignRole(EnsureSuperAdminRole::handle());
        }

        $createdRows = [
            ['ID', (string) $user->id],
            ['Name', $user->name],
            ['Email', $user->email],
        ];

        if ($shouldDesignateSuperAdmin) {
            $createdRows[] = ['Role', EnsureSuperAdminRole::name()];
        }

        $this->table(['Field', 'Value'], $createdRows);

        outro('User created.');

        return SymfonyCommand::SUCCESS;
    }

    /** @return array<int, mixed> */
    private function interactivePasswordRules(): array
    {
        return array_values(
            array_filter(
                PasswordValidationRules::password(),
                static fn (mixed $rule): bool => $rule !== 'confirmed',
            ),
        );
    }
}
