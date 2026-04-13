<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\Requests;

use App\Modules\IAM\Roles\Actions\RoleNameNormalizer;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('roles.create') ?? false;
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
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where(
                    fn (QueryBuilder $query) => $query->whereNull('deleted_at'),
                ),
            ],
            'user_ids' => ['array'],
            'user_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('users', 'id')->where(
                    fn (QueryBuilder $query) => $query->whereNull('deleted_at'),
                ),
            ],
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
