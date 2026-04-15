<?php

declare(strict_types=1);

namespace App\Inertia;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\FileViewFinder;
use InvalidArgumentException;

final readonly class ModulePageFinder
{
    /**
     * @param  array<int, string>  $paths
     * @param  array<int, string>  $extensions
     */
    public function __construct(
        private Filesystem $files,
        private array $paths,
        private array $extensions,
    ) {}

    public function find(string $component): string
    {
        $modulePagePath = $this->findModulePagePath($component);

        if ($modulePagePath !== null) {
            return $modulePagePath;
        }

        return $this->newFinder($this->rootPaths())->find($component);
    }

    private function findModulePagePath(string $component): ?string
    {
        $segments = explode('/', $component);
        $moduleName = array_shift($segments);

        if ($segments === []) {
            return null;
        }

        $modulePath = base_path(sprintf('Modules/%s/resources/js/Pages', $moduleName));

        if (! $this->files->isDirectory($modulePath)) {
            return null;
        }

        try {
            return $this->newFinder([$modulePath])->find(implode('/', $segments));
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /** @return array<int, string> */
    private function rootPaths(): array
    {
        return array_values(array_filter(
            $this->paths,
            static fn (string $path): bool => ! str_contains($path, DIRECTORY_SEPARATOR.'Modules'.DIRECTORY_SEPARATOR),
        ));
    }

    /** @param  array<int, string>  $paths */
    private function newFinder(array $paths): FileViewFinder
    {
        return new FileViewFinder($this->files, $paths, $this->extensions);
    }
}
