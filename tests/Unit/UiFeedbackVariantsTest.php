<?php

declare(strict_types=1);

it('supports feedback variants across shared ui components', function () {
    $projectRoot = dirname(__DIR__, 2);
    $variantFiles = [
        'resources/js/components/ui/alert/index.ts',
        'resources/js/components/ui/badge/index.ts',
        'resources/js/components/ui/button/index.ts',
        'resources/js/components/ui/card/index.ts',
        'resources/js/components/ui/checkbox/index.ts',
        'resources/js/components/ui/input/index.ts',
        'resources/js/components/ui/dialog/index.ts',
        'resources/js/components/ui/tooltip/index.ts',
    ];

    foreach ($variantFiles as $variantFile) {
        $contents = file_get_contents($projectRoot.'/'.$variantFile);

        expect($contents)
            ->toContain('destructive')
            ->toContain('info')
            ->toContain('warning')
            ->toContain('success');
    }
});

it('forwards feedback variants through vue wrappers', function () {
    $projectRoot = dirname(__DIR__, 2);
    $wrapperExpectations = [
        'resources/js/components/ui/checkbox/Checkbox.vue' => [
            'checkboxVariants({ variant: props.variant })',
        ],
        'resources/js/components/ui/badge/Badge.vue' => [
            'badgeVariants({ variant })',
        ],
        'resources/js/components/ui/dialog/DialogContent.vue' => [
            'dialogContentVariants({ variant: props.variant })',
            'dialogCloseVariants({ variant: props.variant })',
        ],
        'resources/js/components/ui/dialog/DialogScrollContent.vue' => [
            'dialogScrollContentVariants({ variant: props.variant })',
            'dialogScrollCloseVariants({ variant: props.variant })',
        ],
        'resources/js/components/ui/tooltip/TooltipContent.vue' => [
            'tooltipContentVariants({ variant: props.variant })',
            'tooltipArrowVariants({ variant: props.variant })',
        ],
        'resources/js/components/ui/button/Button.vue' => [
            'appearance?: ButtonVariants["appearance"]',
            'buttonVariants({ appearance, variant, size, rounded })',
        ],
    ];

    foreach ($wrapperExpectations as $wrapperFile => $expectedStrings) {
        $contents = file_get_contents($projectRoot.'/'.$wrapperFile);

        foreach ($expectedStrings as $expectedString) {
            expect($contents)->toContain($expectedString);
        }
    }
});

it('separates button style from semantic color variants', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/ui/button/index.ts');

    expect($contents)
        ->toContain('appearance: {')
        ->toContain('variant: {')
        ->toContain('filled')
        ->toContain('default')
        ->toContain('muted')
        ->toContain('primary')
        ->toContain('secondary')
        ->toContain('destructive');
});
