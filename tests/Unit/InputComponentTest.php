<?php

declare(strict_types=1);

it('matches the material input variants structure', function () {
    $path = dirname(__DIR__, 2).'/resources/js/components/ui/input/Input.vue';
    $indexPath = dirname(__DIR__, 2).'/resources/js/components/ui/input/index.ts';

    expect(file_exists($path))->toBeTrue();
    expect(file_exists($indexPath))->toBeTrue();

    $contents = file_get_contents($path);
    $indexContents = file_get_contents($indexPath);

    expect($contents)
        ->toContain('inheritAttrs: false')
        ->toContain('leading-icon')
        ->toContain('trailing-icon')
        ->toContain('supportingText')
        ->toContain('maxLength')
        ->toContain('inputVariants')
        ->toContain('inputLabelVariants')
        ->toContain('inputAssistiveTextVariants')
        ->toContain('textarea');

    expect($indexContents)
        ->toContain('variant')
        ->toContain('outlined')
        ->toContain('filled')
        ->toContain('bg-transparent px-0');
});
