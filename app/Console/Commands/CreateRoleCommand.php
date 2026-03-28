<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Admin\Roles\CreateRole;
use App\Models\User;
use App\Support\Data\Admin\Roles\CreateRoleData;
use App\Support\RoleNameNormalizer;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Validation\Rule;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

final class CreateRoleCommand extends BaseInteractiveCreateCommand
{
    protected $signature = 'create:role';

    protected $description = 'Interactively create a role via the CreateRole action.';

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
                        Rule::unique('roles', 'name')->where(
                            fn (QueryBuilder $query) => $query->whereNull('deleted_at'),
                        ),
                    ]],
                    'name',
                    ['name.unique' => 'A role with this name already exists.'],
                );
            },
        );

        $normalizedRoleName = $roleNameNormalizer->normalize($roleNameInput);
        $assignableUsers = User::query()
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->orderBy('email')
            ->get();

        /** @var list<int> $selectedUserIds */
        $selectedUserIds = [];

        if ($assignableUsers->isEmpty()) {
            warning('No users found. This role will be created without assignments.');
        } elseif (confirm(
            label: 'Assign existing users to this role?',
            default: false,
        )) {
            $selectedUserIds = array_map(
                static fn (string|int $id): int => (int) $id,
                multiselect(
                    label: 'Users to assign to this role',
                    options: $assignableUsers
                        ->mapWithKeys(static fn (User $user): array => [
                            $user->id => sprintf('%s <%s>', $user->name, $user->email),
                        ])
                        ->all(),
                    scroll: 10,
                ),
            );
        }

        $assignedUsersPreview = $assignableUsers
            ->whereIn('id', $selectedUserIds)
            ->map(static fn (User $user): string => sprintf('%s <%s>', $user->name, $user->email))
            ->implode(', ');

        $this->table(['Field', 'Value'], [
            ['Name', $normalizedRoleName],
            ['Assigned users', $assignedUsersPreview !== '' ? $assignedUsersPreview : 'None'],
        ]);

        if (! confirm(label: 'Create this role?', default: true)) {
            warning('Role creation cancelled.');

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
