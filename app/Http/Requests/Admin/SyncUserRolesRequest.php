<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SyncUserRolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.assignRoles') ?? false;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'roles' => ['array'],
            'roles.*' => [
                'string',
                'distinct',
                Rule::exists('roles', 'name')->where(
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
            'roles.*.exists' => 'One or more selected roles are invalid.',
        ];
    }
}
