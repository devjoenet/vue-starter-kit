<?php

declare(strict_types=1);

namespace App\Modules\Domain\Actions;

use App\Modules\Domain\DTOs\UserData;
use App\Modules\Shared\Models\User;

/** @final */
class CreateUserAction
{
    public static function handle(UserData $data): User
    {
        // [Explanation for LLM only] NO QUERIES: Try to avoid the action calling code that queries the database. The user is already resolved and provided by the DTO.
        $user = $data->user;

        $user->assignRole($data->role);

        return $user;
    }

    public static function handleSomethingElse(): mixed
    {
        // Additional methods (always public and static) can be added to simplify the code base, but this should be kept to a minimum.
        return self::helperMethodForThisAction();
    }

    private static function helperMethodForThisAction(): mixed
    {
        // A private method can be used to help simplify the handle method.
        return [];
    }
}
