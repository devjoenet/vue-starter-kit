<?php

declare(strict_types=1);

namespace App\Modules\IAM\Actions;

use Illuminate\Support\Str;

final class PermissionNormalizer
{
    /** @return array{group: string, group_label: string, group_description: string|null, name: string, label: string, description: string|null} */
    public function normalize(
        string $group,
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $groupLabel = null,
        ?string $groupDescription = null,
    ): array {
        $normalizedGroup = $this->normalizeGroup($group);
        $normalizedName = $this->normalizeName($name, $normalizedGroup);

        return [
            'group' => $normalizedGroup,
            'group_label' => $this->normalizeGroupLabel($groupLabel, $normalizedGroup),
            'group_description' => $this->normalizeDescription($groupDescription),
            'name' => $normalizedName,
            'label' => $this->normalizeLabel($label, $normalizedName),
            'description' => $this->normalizeDescription($description),
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

    public function normalizeGroupLabel(?string $label, string $group): string
    {
        $normalizedLabel = mb_trim((string) $label);

        if ($normalizedLabel !== '') {
            return Str::of($normalizedLabel)->squish()->title()->toString();
        }

        return Str::of($group)
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    public function normalizeName(string $name, string $fallbackGroup = ''): string
    {
        $rawValue = mb_trim($name);

        if ($rawValue === '') {
            return '';
        }

        $segments = array_values(array_filter(explode('.', $rawValue)));

        if (count($segments) === 1) {
            $namespace = $fallbackGroup;
            $actionSegment = $segments[0];
        } else {
            $namespace = collect(array_slice($segments, 0, -1))
                ->map(fn (string $segment): string => $this->normalizeGroup($segment))
                ->filter()
                ->implode('.');
            $actionSegment = (string) end($segments);
        }

        $action = Str::of($actionSegment)->squish()->camel()->toString();

        if ($namespace === '' || $action === '') {
            return '';
        }

        return sprintf('%s.%s', $namespace, $action);
    }

    public function normalizeLabel(?string $label, string $name): string
    {
        $normalizedLabel = mb_trim((string) $label);

        if ($normalizedLabel !== '') {
            return Str::of($normalizedLabel)->squish()->toString();
        }

        return $this->labelFromPermissionKey($name);
    }

    public function normalizeDescription(?string $description): ?string
    {
        $normalizedDescription = mb_trim((string) $description);

        return $normalizedDescription !== ''
            ? Str::of($normalizedDescription)->squish()->toString()
            : null;
    }

    public function labelFromPermissionKey(string $name): string
    {
        $segments = array_values(array_filter(explode('.', $name)));
        $action = (string) (end($segments) ?: $name);
        $headlineReady = preg_replace('/([a-z])([A-Z])/', '$1 $2', $action) ?? $action;

        return Str::of($headlineReady)
            ->replace(['_', '.'], ' ')
            ->headline()
            ->toString();
    }
}
