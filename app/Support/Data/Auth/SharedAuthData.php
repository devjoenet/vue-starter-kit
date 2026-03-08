<?php

declare(strict_types=1);

namespace App\Support\Data\Auth;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

final class SharedAuthData extends Data
{
    /**
     * @param  list<string>  $roles
     * @param  list<string>  $permissions
     */
    public function __construct(
        public ?AuthenticatedUserData $user,
        public array $roles,
        public array $permissions,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $user = $request->user();

        return new self(
            user: $user ? AuthenticatedUserData::fromModel($user) : null,
            roles: $user?->getRoleNames()->values()->all() ?? [],
            permissions: $user?->getAllPermissions()->pluck('name')->values()->all() ?? [],
        );
    }
}
