<?php

declare(strict_types=1);

namespace App\Modules\Admin\Users\Queries;

use App\Models\Role;
use App\Models\User;
use App\Modules\Admin\Users\DTOs\UserIndexFilterOptionsData;

final class GetUserFilterOptions
{
    public static function handle(): UserIndexFilterOptionsData
    {
        return UserIndexFilterOptionsData::from([
            'name' => self::distinctUserValues('name'),
            'email' => self::distinctUserValues('email'),
            'roles' => Role::query()
                ->select('name')
                ->orderBy('name')
                ->pluck('name')
                ->all(),
        ]);
    }

    /**
     * @return list<string>
     */
    private static function distinctUserValues(string $column): array
    {
        return User::query()
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->all();
    }
}
