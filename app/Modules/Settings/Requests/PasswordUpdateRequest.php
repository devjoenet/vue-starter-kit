<?php

declare(strict_types=1);

namespace App\Modules\Settings\Requests;

use App\Modules\Shared\Actions\PasswordValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PasswordUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'current_password' => PasswordValidationRules::currentPassword(),
            'password' => PasswordValidationRules::password(),
        ];
    }
}
