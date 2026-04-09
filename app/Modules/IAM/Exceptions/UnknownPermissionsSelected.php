<?php

declare(strict_types=1);

namespace App\Modules\IAM\Exceptions;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class UnknownPermissionsSelected extends ValidationException
{
    /** @param  list<string>  $permissionNames */
    private function __construct(
        public readonly array $permissionNames,
    ) {
        $validator = Validator::make([], []);
        $validator->errors()->add('permissions', $this->validationMessage());

        parent::__construct($validator);
    }

    /** @param  list<string>  $permissionNames */
    public static function fromNames(array $permissionNames): self
    {
        return new self($permissionNames);
    }

    public function validationMessage(): string
    {
        return 'One or more selected permissions are invalid.';
    }
}
