<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class SyncRolePermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'permissions' => ['array'],
            'permissions.*' => ['string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'permissions.*.max' => 'Permission names may not be greater than 255 characters.',
        ];
    }
}
