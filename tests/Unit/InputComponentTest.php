<?php

declare(strict_types=1);

it('matches the enhanced input variants structure', function () {
    $path = dirname(__DIR__, 2).'/resources/js/components/ui/input/Input.vue';
    $variantsPath = dirname(__DIR__, 2).'/resources/js/components/ui/input/variants.ts';

    expect(file_exists($path))->toBeTrue();
    expect(file_exists($variantsPath))->toBeTrue();

    $contents = file_get_contents($path);
    $variantsContents = file_get_contents($variantsPath);

    expect($contents)
        ->toContain('inheritAttrs: false')
        ->toContain('clearable?: boolean')
        ->toContain('preventAutoFill?: boolean')
        ->toContain('handleCompositionStart')
        ->toContain('handleCompositionEnd')
        ->toContain('normalizeNumberValue')
        ->toContain('slot name="clear-icon"')
        ->toContain('defineExpose')
        ->toContain('textarea');

    expect($variantsContents)
        ->toContain('variant')
        ->toContain('outlined')
        ->toContain('filled')
        ->toContain('rounded-b-none')
        ->toContain('bg-transparent px-0');
});
