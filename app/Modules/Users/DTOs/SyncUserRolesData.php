<?php

declare(strict_types=1);

namespace App\Modules\Users\DTOs;

use Spatie\LaravelData\Data;

final class SyncUserRolesData extends Data
{
    /** @param  list<string>  $roles */
    public function __construct(
        public array $roles,
    ) {
        /** @var list<string> $roles */
        $roles = collect($roles)
            ->unique()
            ->values()
            ->all();

        $this->roles = $roles;
    }

    /** @param  array{roles?: list<string>}  $input */
    public static function fromInput(array $input): self
    {
        return new self(
            roles: $input['roles'] ?? [],
        );
    }

    /** @return list<string> */
    public function roleNames(): array
    {
        return $this->roles;
    }
}
