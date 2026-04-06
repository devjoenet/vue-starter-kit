<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Features;
use Laravel\Fortify\InteractsWithTwoFactorState;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TwoFactorAuthenticationRequest extends FormRequest
{
    use InteractsWithTwoFactorState;

    public function authorize(): bool
    {
        return Features::enabled(Features::twoFactorAuthentication()) && $this->user() !== null;
    }

    /** @return array<string, ValidationRule|array|string> */
    public function rules(): array
    {
        return [];
    }
}
