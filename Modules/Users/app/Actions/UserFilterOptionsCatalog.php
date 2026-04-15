<?php

declare(strict_types=1);

namespace Modules\Users\Actions;

use Modules\Core\Models\User;
use Modules\Roles\Models\Role;
use Modules\Users\Contracts\UserFilterOptionsProvider;
use Modules\Users\DTOs\UserIndexFilterOptionsData;

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
