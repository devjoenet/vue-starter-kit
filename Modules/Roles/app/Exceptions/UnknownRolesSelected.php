<?php

declare(strict_types=1);

namespace Modules\Roles\Exceptions;

use Modules\Core\Exceptions\AbstractIamValidationException;

final class UnknownRolesSelected extends AbstractIamValidationException
{
    /** @param  list<string>  $roleNames */
    private function __construct(
        public readonly array $roleNames,
    ) {
        parent::__construct(
            errors: ['roles' => [$this->validationMessage()]],
            context: ['role_names' => $this->roleNames],
        );
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
