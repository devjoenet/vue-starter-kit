<?php

declare(strict_types=1);

namespace App\Modules\IAM\Users\Actions;

use App\Modules\IAM\Users\DTOs\AssignableUserData;
use App\Modules\Shared\Models\User;
use Illuminate\Support\Collection;

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
