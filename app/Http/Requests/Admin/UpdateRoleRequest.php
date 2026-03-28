<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Role;
use App\Support\RoleNameNormalizer;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => app(RoleNameNormalizer::class)->normalize((string) $this->input('name')),
        ]);
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        /** @var Role|null $role */
        $role = $this->route('role');
        $nameUniqueRule = Rule::unique('roles', 'name')->where(
            fn (QueryBuilder $query) => $query->whereNull('deleted_at'),
        );

        if ($role !== null) {
            $nameUniqueRule->ignore($role->getKey());
        }

        return [
            'name' => ['required', 'string', 'max:255', $nameUniqueRule],
        ];
    }

    /** @return array<string, string> */
    #[Override]
    public function messages(): array
    {
        return [
            'name.unique' => 'A role with this name already exists.',
        ];
    }
}
