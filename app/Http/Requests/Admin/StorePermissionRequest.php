<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Support\PermissionNormalizer;
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
        $normalizedPermission = app(PermissionNormalizer::class)->normalize(
            (string) $this->input('group'),
            (string) $this->input('name'),
        );

        $this->merge([
            'name' => $normalizedPermission['name'],
            'group' => $normalizedPermission['group'],
        ]);
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')],
            'group' => ['required', 'string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    #[Override]
    public function messages(): array
    {
        return [
            'name.unique' => 'A permission with this name already exists.',
        ];
    }
}
