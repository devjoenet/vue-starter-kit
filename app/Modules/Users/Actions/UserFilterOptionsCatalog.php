<?php

declare(strict_types=1);

namespace App\Modules\Users\Actions;

use App\Modules\Roles\Models\Role;
use App\Modules\Users\Contracts\UserFilterOptionsProvider;
use App\Modules\Users\DTOs\UserIndexFilterOptionsData;
use App\Modules\Users\Models\User;

final class UserFilterOptionsCatalog implements UserFilterOptionsProvider
{
    public function options(): UserIndexFilterOptionsData
    {
        return UserIndexFilterOptionsData::from([
            'name' => $this->distinctUserValues('name'),
            'email' => $this->distinctUserValues('email'),
            'roles' => Role::query()
                ->select('name')
                ->orderBy('name')
                ->pluck('name')
                ->all(),
        ]);
    }

    /** @return list<string> */
    private function distinctUserValues(string $column): array
    {
        return User::query()
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->all();
    }
}
