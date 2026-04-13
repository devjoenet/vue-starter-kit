<?php

declare(strict_types=1);

namespace App\Modules\IAM\Roles\DTOs;

use App\Modules\IAM\Roles\Requests\UpdateRoleRequest;
use Spatie\LaravelData\Data;

final class UpdateRoleData extends Data
{
    public function __construct(public string $name) {}

    public static function fromRequest(UpdateRoleRequest $request): self
    {
        return new self($request->name);
    }
}
