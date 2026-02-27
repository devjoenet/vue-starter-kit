<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SyncUserRolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'roles' => ['array'],
            'roles.*' => ['string', 'distinct', Rule::exists('roles', 'name')],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'roles.*.exists' => 'One or more selected roles are invalid.',
        ];
    }
}
