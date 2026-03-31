<?php

declare(strict_types=1);

namespace App\Modules\Roles\Actions;

use App\Modules\Roles\DTOs\AssignableUserData;
use App\Modules\Users\Models\User;

final class GetAssignableUsers
{
    /** @return list<array{id: int, name: string, email: string}> */
    public static function handle(): array
    {
        return User::query()
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->orderBy('email')
            ->get()
            ->map(fn (User $user): array => AssignableUserData::fromModel($user)->all())
            ->all();
    }
}
