<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

final class PermissionNormalizer
{
    /** @return array{group: string, name: string} */
    public function normalize(string $group, string $name): array
    {
        $normalizedGroup = $this->normalizeGroup($group);

        return [
            'group' => $normalizedGroup,
            'name' => $this->normalizeName($normalizedGroup, $name),
        ];
    }

    public function normalizeGroup(string $group): string
    {
        return Str::of($group)
            ->trim()
            ->replaceMatches('/[^A-Za-z0-9]+/', '_')
            ->lower()
            ->trim('_')
            ->toString();
    }

    public function normalizeName(string $group, string $name): string
    {
        $segment = mb_trim($name);

        if (str_contains($segment, '.')) {
            $parts = array_values(array_filter(explode('.', $segment)));
            $segment = (string) (end($parts) ?: '');
        }

        $action = Str::of($segment)->squish()->camel()->toString();

        if ($action === '') {
            return '';
        }

        return sprintf('%s.%s', $group, $action);
    }
}
