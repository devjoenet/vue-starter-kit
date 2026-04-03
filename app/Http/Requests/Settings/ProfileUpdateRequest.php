<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use App\Modules\Users\Actions\ProfileValidationRules;
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
        return ProfileValidationRules::profile($this->user()->id);
    }
}
