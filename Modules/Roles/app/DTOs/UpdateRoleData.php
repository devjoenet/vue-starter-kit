<?php

declare(strict_types=1);

namespace Modules\Roles\DTOs;

use Modules\Roles\Http\Requests\UpdateRoleRequest;
use Spatie\LaravelData\Data;

final class UpdateRoleData extends Data
{
    public function __construct(public string $name) {}

    public static function fromRequest(UpdateRoleRequest $request): self
    {
        return new self($request->name);
    }
}
