<?php

declare(strict_types=1);

it('enables vite asset prefetching for the inertia app shell', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/app/Providers/AppServiceProvider.php');

    expect($contents)
        ->toContain('use Illuminate\Support\Facades\Vite;')
        ->toContain("Vite::prefetch(concurrency: (int) config('performance.observability.vite_prefetch_concurrency', 3));");
});

it('prefetches key workspace navigation links for faster inertia visits', function () {
    $projectRoot = dirname(__DIR__, 2);
    $files = [
        'resources/js/components/NavMain.vue',
        'resources/js/components/app-header/AppHeaderDesktopNavigation.vue',
        'resources/js/components/admin/dashboard/DashboardAnchorWidget.vue',
        'resources/js/components/admin/dashboard/DashboardActionWidget.vue',
        'resources/js/components/UserMenuContent.vue',
    ];

    foreach ($files as $file) {
        $contents = file_get_contents($projectRoot.'/'.$file);

        expect($contents)->toContain(":prefetch=\"['hover', 'click']\"");
    }
});
