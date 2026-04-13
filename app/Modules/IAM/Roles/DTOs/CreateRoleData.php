<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\DTOs;

use App\Modules\IAM\Roles\Requests\StoreRoleRequest;
use Spatie\LaravelData\Data;

final class CreateRoleData extends Data
{
    /** @param  list<int>  $user_ids */
    public function __construct(
        public string $name,
        public array $user_ids = [],
    ) {}

    public static function fromRequest(StoreRoleRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            user_ids: $request->validated('user_ids', []),
        );
    }
}
