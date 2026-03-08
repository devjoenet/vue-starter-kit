<?php

declare(strict_types=1);

namespace App\Support;

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
