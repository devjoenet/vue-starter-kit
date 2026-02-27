<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
        $emailUniqueRule = Rule::unique('users', 'email');

        if ($user !== null) {
            $emailUniqueRule->ignore($user->getKey());
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailUniqueRule],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string', 'min:8', 'required_with:password'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.unique' => 'A user with this email address already exists.',
        ];
    }
}
