<?php

declare(strict_types=1);

namespace Modules\Users\Actions;

use Illuminate\Support\Collection;
use Modules\Core\Models\User;
use Modules\Users\DTOs\AssignableUserData;

final class GetAssignableUsers
{
    /** @return Collection<int, AssignableUserData> */
    public static function handle(): Collection
    {
        /** @var Collection<int, AssignableUserData> $users */
        $users = AssignableUserData::collect(
            User::query()
                ->select(['id', 'name', 'email'])
                ->orderBy('name')
                ->orderBy('email')
                ->get(),
            Collection::class,
        );

        return $users;
    }
}
