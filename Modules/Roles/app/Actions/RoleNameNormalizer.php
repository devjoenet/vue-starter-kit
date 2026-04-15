<?php

declare(strict_types=1);

namespace Modules\Roles\Actions;

use Illuminate\Support\Str;

final class RoleNameNormalizer
{
    public function normalize(string $name): string
    {
        return Str::of($name)
            ->squish()
            ->kebab()
            ->toString();
    }
}
