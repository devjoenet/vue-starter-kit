<?php

declare(strict_types=1);

it('uses destructive base variants instead of legacy error wrappers in app views', function () {
    $projectRoot = dirname(__DIR__, 2);
    $viewRoots = [
        $projectRoot.'/resources/js/components',
        $projectRoot.'/resources/js/layouts',
        $projectRoot.'/resources/js/pages',
    ];

    foreach ($viewRoots as $viewRoot) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewRoot));

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile()) {
                continue;
            }

            if ($fileInfo->getExtension() !== 'vue') {
                continue;
            }

            $filePath = $fileInfo->getPathname();

            if (str_ends_with($filePath, 'AlertError.vue') || str_ends_with($filePath, 'InputError.vue')) {
                continue;
            }

            $contents = file_get_contents($filePath);

            expect($contents)->not->toContain('AlertError');
            expect($contents)->not->toContain('InputError');
        }
    }
});

it('uses the correct role permission gate in admin layout navigation', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/layouts/AdminLayout.vue');

    expect($contents)->toContain('can("roles.view")');
    expect($contents)->not->toContain('can("users.view") ? [{ label: "Roles"');
});

it('reuses the shared permission group field component across admin permission pages', function () {
    $projectRoot = dirname(__DIR__, 2);
    $permissionPages = [
        'resources/js/pages/admin/Permissions/Create.vue',
        'resources/js/pages/admin/Permissions/Edit.vue',
    ];

    foreach ($permissionPages as $permissionPage) {
        $contents = file_get_contents($projectRoot.'/'.$permissionPage);

        expect($contents)->toContain('PermissionGroupSelect');
    }
});

it('uses dedicated create and edit pages for admin CRUD forms', function () {
    $projectRoot = dirname(__DIR__, 2);
    $indexPages = [
        'resources/js/pages/admin/Users/Index.vue',
        'resources/js/pages/admin/Roles/Index.vue',
        'resources/js/pages/admin/Permissions/Index.vue',
    ];

    foreach ($indexPages as $indexPage) {
        $contents = file_get_contents($projectRoot.'/'.$indexPage);

        expect($contents)->not->toContain('DialogScrollContent');
        expect($contents)->not->toContain('DialogHeader');
    }
});

it('uses collapsible permission groups in the role management edit page', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Roles/Edit.vue');

    expect($contents)->toContain('Collapsible');
    expect($contents)->toContain('CollapsibleTrigger');
    expect($contents)->toContain(':default-open="true"');
});
