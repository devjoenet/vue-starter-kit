<?php

declare(strict_types=1);

namespace App\Modules\IAM\DTOs;

use App\Modules\IAM\Requests\SyncRolePermissionsRequest;
use Spatie\LaravelData\Data;

final class SyncRolePermissionsData extends Data
{
    /** @param  list<string>  $permissions */
    public function __construct(public array $permissions) {}

    public static function fromRequest(SyncRolePermissionsRequest $request): self
    {
        return new self($request->validated('permissions', []));
    }
}
