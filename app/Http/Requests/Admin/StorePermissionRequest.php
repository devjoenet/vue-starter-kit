<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Modules\Permissions\Actions\PermissionNormalizer;
use App\Modules\Permissions\Models\PermissionGroup;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[Override]
    protected function prepareForValidation(): void
    {
        $permissionNormalizer = app(PermissionNormalizer::class);
        $normalizedGroup = $permissionNormalizer->normalizeGroup((string) $this->input('group'));
        $existingGroup = PermissionGroup::withTrashed()
            ->where('slug', $normalizedGroup)
            ->first();

        $normalizedPermission = $permissionNormalizer->normalize(
            $normalizedGroup,
            (string) $this->input('name'),
            $this->input('label'),
            $this->input('description'),
            $this->filled('group_label') ? $this->input('group_label') : $existingGroup?->label,
            $this->filled('group_description') ? $this->input('group_description') : $existingGroup?->description,
        );

        $this->replace([
            ...$this->all(),
            ...$normalizedPermission,
        ]);
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9_]+(?:\.[A-Za-z][A-Za-z0-9]*)+$/',
                Rule::unique('permissions', 'name')->where(
                    fn (QueryBuilder $query) => $query->whereNull('deleted_at'),
                ),
            ],
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'group' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/'],
            'group_label' => ['required', 'string', 'max:255'],
            'group_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    #[Override]
    public function messages(): array
    {
        return [
            'name.unique' => 'A permission with this name already exists.',
            'name.regex' => 'Permission keys must look like users.view or reports.exportData.',
            'group.regex' => 'Permission groups must use lowercase letters, numbers, or underscores.',
        ];
    }
}
