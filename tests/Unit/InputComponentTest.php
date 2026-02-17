<?php

declare(strict_types=1);

it('matches the enhanced input variants structure', function () {
    $path = dirname(__DIR__, 2).'/resources/js/components/ui/input/Input.vue';
    $indexPath = dirname(__DIR__, 2).'/resources/js/components/ui/input/index.ts';

    expect(file_exists($path))->toBeTrue();
    expect(file_exists($indexPath))->toBeTrue();

    $contents = file_get_contents($path);
    $indexContents = file_get_contents($indexPath);

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

    expect($indexContents)
        ->toContain('variant')
        ->toContain('outlined')
        ->toContain('filled')
        ->toContain('rounded-b-none')
        ->toContain('bg-transparent px-0');
});
