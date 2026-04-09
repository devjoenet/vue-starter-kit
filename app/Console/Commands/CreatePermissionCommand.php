<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Modules\IAM\Actions\CreatePermission;
use App\Modules\IAM\Actions\PermissionNormalizer;
use App\Modules\IAM\Contracts\PermissionGroupCatalogContract;
use App\Modules\IAM\DTOs\CreatePermissionData;
use App\Modules\IAM\Models\PermissionGroup;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;

final class CreatePermissionCommand extends BaseInteractiveCreateCommand
{
    protected $signature = 'create:permission';

    protected $description = 'Interactively create a permission via the CreatePermission action.';

    public function handle(
        PermissionNormalizer $permissionNormalizer,
        PermissionGroupCatalogContract $permissionGroupCatalog,
    ): int {
        intro('Create a permission');

        $knownGroups = PermissionGroup::query()
            ->withTrashed()
            ->orderBy('label')
            ->get()
            ->keyBy('slug');

        $groupInput = text(
            label: 'Permission group',
            placeholder: 'users',
            validate: fn (string $value): ?string => $this->validationMessage(
                ['group' => $permissionNormalizer->normalizeGroup($value)],
                ['group' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/']],
                'group',
            ),
        );

        $normalizedGroup = $permissionNormalizer->normalizeGroup($groupInput);
        /** @var PermissionGroup|null $existingGroup */
        $existingGroup = $knownGroups->get($normalizedGroup);
        $defaultGroupLabel = $existingGroup instanceof PermissionGroup
            ? $existingGroup->label
            : $permissionNormalizer->normalizeGroupLabel(null, $normalizedGroup);
        $defaultGroupDescription = $existingGroup instanceof PermissionGroup
            ? ($existingGroup->description ?? '')
            : '';

        $groupLabelInput = text(
            label: 'Group label',
            default: $defaultGroupLabel,
            validate: fn (string $value): ?string => $this->validationMessage(
                ['group_label' => $permissionNormalizer->normalizeGroupLabel($value, $normalizedGroup)],
                ['group_label' => ['required', 'string', 'max:255']],
                'group_label',
            ),
        );

        $groupDescriptionInput = text(
            label: 'Group description (optional)',
            placeholder: 'What does this group govern?',
            default: $defaultGroupDescription,
            validate: fn (string $value): ?string => $this->validationMessage(
                ['group_description' => $permissionNormalizer->normalizeDescription($value)],
                ['group_description' => ['nullable', 'string', 'max:500']],
                'group_description',
            ),
        );

        $permissionKeyInput = text(
            label: 'Permission key or action',
            placeholder: 'create or users.create',
            validate: function (string $value) use (
                $groupInput,
                $groupLabelInput,
                $groupDescriptionInput,
                $permissionNormalizer,
            ): ?string {
                $normalizedPermission = $permissionNormalizer->normalize(
                    $groupInput,
                    $value,
                    null,
                    null,
                    $groupLabelInput,
                    $groupDescriptionInput,
                );

                return $this->validationMessage(
                    $normalizedPermission,
                    [
                        'name' => [
                            'required',
                            'string',
                            'max:255',
                            'regex:/^[a-z0-9_]+(?:\.[A-Za-z][A-Za-z0-9]*)+$/',
                            $this->activeUniqueRule('permissions', 'name'),
                        ],
                    ],
                    'name',
                    [
                        'name.unique' => 'A permission with this name already exists.',
                        'name.regex' => 'Permission keys must look like users.view or reports.exportData.',
                    ],
                );
            },
        );

        $normalizedPermissionPreview = $permissionNormalizer->normalize(
            $groupInput,
            $permissionKeyInput,
            null,
            null,
            $groupLabelInput,
            $groupDescriptionInput,
        );

        $permissionLabelInput = text(
            label: 'Permission label',
            default: $normalizedPermissionPreview['label'],
            validate: fn (string $value): ?string => $this->validationMessage(
                ['label' => $permissionNormalizer->normalizeLabel($value, $normalizedPermissionPreview['name'])],
                ['label' => ['required', 'string', 'max:255']],
                'label',
            ),
        );

        $permissionDescriptionInput = text(
            label: 'Permission description (optional)',
            placeholder: 'What does this permission allow?',
            validate: fn (string $value): ?string => $this->validationMessage(
                ['description' => $permissionNormalizer->normalizeDescription($value)],
                ['description' => ['nullable', 'string', 'max:500']],
                'description',
            ),
        );

        $normalizedPermission = $permissionNormalizer->normalize(
            $groupInput,
            $permissionKeyInput,
            $permissionLabelInput,
            $permissionDescriptionInput,
            $groupLabelInput,
            $groupDescriptionInput,
        );

        $this->table(['Field', 'Value'], [
            ['Group slug', $normalizedPermission['group']],
            ['Group label', $normalizedPermission['group_label']],
            ['Group description', $this->presentValue($normalizedPermission['group_description'], '—')],
            ['Permission key', $normalizedPermission['name']],
            ['Permission label', $normalizedPermission['label']],
            ['Permission description', $this->presentValue($normalizedPermission['description'], '—')],
        ]);

        if (! $this->confirmsOrCancels('Create this permission?', 'Permission creation cancelled.')) {
            return SymfonyCommand::SUCCESS;
        }

        $permission = CreatePermission::handle(new CreatePermissionData(
            name: $normalizedPermission['name'],
            label: $normalizedPermission['label'],
            description: $normalizedPermission['description'],
            group: $normalizedPermission['group'],
            groupLabel: $normalizedPermission['group_label'],
            groupDescription: $normalizedPermission['group_description'],
        ), $permissionGroupCatalog);

        $this->table(['ID', 'Group', 'Permission'], [
            [$permission->id, $permission->group_label, $permission->display_label],
        ]);

        outro('Permission created.');

        return SymfonyCommand::SUCCESS;
    }
}
