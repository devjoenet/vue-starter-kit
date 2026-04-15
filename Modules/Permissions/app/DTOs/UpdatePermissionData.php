<?php

declare(strict_types=1);

namespace Modules\Permissions\DTOs;

use Modules\Permissions\Http\Requests\UpdatePermissionRequest;
use Spatie\LaravelData\Data;

final class UpdatePermissionData extends Data
{
    public function __construct(
        public string $label,
        public ?string $description,
        public string $group,
        public string $groupLabel,
        public ?string $groupDescription,
    ) {}

    public static function fromRequest(UpdatePermissionRequest $request): self
    {
        return new self(
            label: (string) $request->validated('label'),
            description: $request->validated('description'),
            group: (string) $request->validated('group'),
            groupLabel: (string) $request->validated('group_label'),
            groupDescription: $request->validated('group_description'),
        );
    }
}
