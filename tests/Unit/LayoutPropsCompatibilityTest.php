<?php

declare(strict_types=1);

it('uses supported inertia layout props in the app layout', function () {
    $path = dirname(__DIR__, 2).'/resources/js/layouts/AppLayout.vue';

    expect(file_exists($path))->toBeTrue();

    $contents = file_get_contents($path);

    expect($contents)
        ->toContain('type Props = {')
        ->toContain('breadcrumbs?: BreadcrumbItem[];')
        ->toContain('withDefaults(defineProps<Props>(), {')
        ->toContain('breadcrumbs: () => [],')
        ->toContain('<AppSidebarLayout :breadcrumbs="breadcrumbs">')
        ->not->toContain('useLayoutProps')
        ->not->toContain("from '@inertiajs/vue3'");
});
