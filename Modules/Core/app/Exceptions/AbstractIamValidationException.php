<?php

declare(strict_types=1);

namespace Modules\Core\Exceptions;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class AbstractIamValidationException extends ValidationException
{
    /**
     * @param  array<string, list<string>>  $errors
     * @param  array<string, mixed>  $context
     */
    protected function __construct(
        array $errors,
        private readonly array $context = [],
    ) {
        $validator = Validator::make([], []);

        foreach ($errors as $field => $messages) {
            foreach ($messages as $message) {
                $validator->errors()->add($field, $message);
            }
        }

        parent::__construct($validator);
    }

    /** @return array<string, mixed> */
    public function context(): array
    {
        return $this->context + [
            'error_fields' => array_keys($this->errors()),
        ];
    }
}
