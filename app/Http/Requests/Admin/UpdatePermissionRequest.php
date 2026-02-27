<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $group = $this->normalizePermissionGroup((string) $this->input('group'));
        $name = $this->normalizePermissionName($group, (string) $this->input('name'));

        $this->merge([
            'name' => $name,
            'group' => $group,
        ]);
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        /** @var Permission|null $permission */
        $permission = $this->route('permission');
        $nameUniqueRule = Rule::unique('permissions', 'name');

        if ($permission !== null) {
            $nameUniqueRule->ignore($permission->getKey());
        }

        return [
            'name' => ['required', 'string', 'max:255', $nameUniqueRule],
            'group' => ['required', 'string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.unique' => 'A permission with this name already exists.',
        ];
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
