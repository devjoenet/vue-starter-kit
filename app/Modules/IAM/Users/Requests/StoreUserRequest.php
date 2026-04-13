<?php

declare(strict_types=1);

namespace App\Modules\IAM\Users\Requests;

use App\Modules\Shared\Actions\UserIdentityValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Override;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.create') ?? false;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            ...UserIdentityValidationRules::identity(),
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
        ];
    }

    /** @return array<string, string> */
    #[Override]
    public function messages(): array
    {
        return [
            'email.unique' => 'A user with this email address already exists.',
        ];
    }
}
