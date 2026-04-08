<?php

declare(strict_types=1);

namespace App\Modules\IAM\DTOs;

use App\Modules\IAM\Requests\UpdateRoleRequest;
use Spatie\LaravelData\Data;

final class UpdateRoleData extends Data
{
    public function __construct(public string $name) {}

    public static function fromRequest(UpdateRoleRequest $request): self
    {
        return new self($request->name);
    }
}
