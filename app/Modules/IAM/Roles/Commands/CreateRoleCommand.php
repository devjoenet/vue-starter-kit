<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Commands;

use App\Modules\IAM\Commands\BaseInteractiveCreateCommand;
use App\Modules\IAM\Roles\Actions\CreateRole;
use App\Modules\IAM\Roles\Actions\RoleNameNormalizer;
use App\Modules\IAM\Roles\DTOs\CreateRoleData;
use App\Modules\IAM\Users\Actions\GetAssignableUsers;
use App\Modules\IAM\Users\DTOs\AssignableUserData;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

#[Signature('create:role')]
#[Description('Interactively create a role via the CreateRole action.')]
final class CreateRoleCommand extends BaseInteractiveCreateCommand
{
    public function handle(RoleNameNormalizer $roleNameNormalizer): int
    {
        intro('Create a role');

        $roleNameInput = text(
            label: 'Role name',
            placeholder: 'support manager',
            validate: function (string $value) use ($roleNameNormalizer): ?string {
                $normalizedName = $roleNameNormalizer->normalize($value);

                return $this->validationMessage(
                    ['name' => $normalizedName],
                    ['name' => [
                        'required',
                        'string',
                        'max:255',
                        $this->activeUniqueRule('roles', 'name'),
                    ]],
                    'name',
                    ['name.unique' => 'A role with this name already exists.'],
                );
            },
        );

        $normalizedRoleName = $roleNameNormalizer->normalize($roleNameInput);
        $assignableUserOptions = GetAssignableUsers::handle()
            ->mapWithKeys(
                static fn (AssignableUserData $user): array => [
                    $user->id => sprintf('%s <%s>', $user->name, $user->email),
                ],
            )
            ->all();

        /** @var list<int> $selectedUserIds */
        $selectedUserIds = [];

        if ($assignableUserOptions === []) {
            warning('No users found. This role will be created without assignments.');
        } elseif (confirm(
            label: 'Assign existing users to this role?',
            default: false,
        )) {
            $selectedUserIds = array_map(
                static fn (string|int $id): int => (int) $id,
                multiselect(
                    label: 'Users to assign to this role',
                    options: $assignableUserOptions,
                    scroll: 10,
                ),
            );
        }

        $assignedUsersPreview = collect($selectedUserIds)
            ->map(static fn (int $userId): ?string => $assignableUserOptions[$userId] ?? null)
            ->filter()
            ->implode(', ');

        $this->table(['Field', 'Value'], [
            ['Name', $normalizedRoleName],
            ['Assigned users', $this->presentValue($assignedUsersPreview)],
        ]);

        if (! $this->confirmsOrCancels('Create this role?', 'Role creation cancelled.')) {
            return SymfonyCommand::SUCCESS;
        }

        $role = CreateRole::handle(new CreateRoleData(
            name: $normalizedRoleName,
            user_ids: $selectedUserIds,
        ));

        $this->table(['ID', 'Name', 'Assigned Users'], [
            [$role->id, $role->name, (string) count($selectedUserIds)],
        ]);

        outro('Role created.');

        return SymfonyCommand::SUCCESS;
    }
}
