<?php

declare(strict_types=1);

namespace App\Modules\Settings\Requests;

use App\Modules\Shared\Actions\UserIdentityValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return UserIdentityValidationRules::identity($this->user()->id);
    }
}
