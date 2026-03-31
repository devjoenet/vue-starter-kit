<?php

declare(strict_types=1);

namespace App\Modules\Users\Exceptions;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class UnknownRolesSelected extends ValidationException
{
    /** @param  list<string>  $roleNames */
    private function __construct(
        public readonly array $roleNames,
    ) {
        $validator = Validator::make([], []);
        $validator->errors()->add('roles', $this->validationMessage());

        parent::__construct($validator);
    }

    /** @param  list<string>  $roleNames */
    public static function fromNames(array $roleNames): self
    {
        return new self($roleNames);
    }

    public function validationMessage(): string
    {
        return 'One or more selected roles are invalid.';
    }
}
