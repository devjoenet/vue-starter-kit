<?php

declare(strict_types=1);

namespace App\Modules\Audit\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class IndexAuditLogsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('audit_logs.view') ?? false;
    }

    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'sort' => ['nullable', 'string', Rule::in(['created_at', 'event', 'actor', 'subject'])],
            'direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
            'actors' => ['nullable', 'array'],
            'actors.*' => ['string', 'max:255'],
            'events' => ['nullable', 'array'],
            'events.*' => ['string', 'max:255'],
            'subject_types' => ['nullable', 'array'],
            'subject_types.*' => ['string', 'max:255'],
            'search' => ['nullable', 'string', 'max:255'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'until' => ['nullable', 'date_format:Y-m-d'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
