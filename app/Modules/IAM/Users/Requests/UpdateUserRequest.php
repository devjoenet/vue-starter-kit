<?php

declare(strict_types=1);

namespace App\Modules\IAM\Users\Requests;

use App\Modules\Shared\Actions\UserIdentityValidationRules;
use App\Modules\Shared\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Override;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.update') ?? false;
    }

    #[Override]
    protected function prepareForValidation(): void
    {
        if (mb_trim((string) $this->input('password', '')) === '') {
            $this->merge(['password' => null, 'password_confirmation' => null]);
        }
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');

        return [
            ...UserIdentityValidationRules::identity($user?->id),
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string', 'min:8', 'required_with:password'],
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
