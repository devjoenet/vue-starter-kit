<?php

declare(strict_types=1);

namespace Modules\Permissions\Exceptions;

use Modules\Core\Exceptions\AbstractIamValidationException;

final class UnknownPermissionsSelected extends AbstractIamValidationException
{
    /** @param  list<string>  $permissionNames */
    private function __construct(
        public readonly array $permissionNames,
    ) {
        parent::__construct(
            errors: ['permissions' => [$this->validationMessage()]],
            context: ['permission_names' => $this->permissionNames],
        );
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
