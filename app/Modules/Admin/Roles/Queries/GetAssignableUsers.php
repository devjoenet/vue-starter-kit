<?php

declare(strict_types=1);

namespace App\Modules\Admin\Roles\Queries;

use App\Models\User;
use App\Modules\Admin\Roles\DTOs\AssignableUserData;

final class GetAssignableUsers
{
    /**
     * @return list<array{id: int, name: string, email: string}>
     */
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
