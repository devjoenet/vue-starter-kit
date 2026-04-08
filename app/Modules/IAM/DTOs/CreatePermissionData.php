<?php

declare(strict_types=1);

namespace App\Modules\IAM\DTOs;

use App\Modules\IAM\Requests\StorePermissionRequest;
use Spatie\LaravelData\Data;

final class CreatePermissionData extends Data
{
    public function __construct(
        public string $name,
        public string $label,
        public ?string $description,
        public string $group,
        public string $groupLabel,
        public ?string $groupDescription,
    ) {}

    public static function fromRequest(StorePermissionRequest $request): self
    {
        return new self(
            name: (string) $request->validated('name'),
            label: (string) $request->validated('label'),
            description: $request->validated('description'),
            group: (string) $request->validated('group'),
            groupLabel: (string) $request->validated('group_label'),
            groupDescription: $request->validated('group_description')
        );
    }
}
