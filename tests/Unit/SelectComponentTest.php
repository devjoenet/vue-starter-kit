<?php

declare(strict_types=1);

it('matches the enhanced select variants structure', function () {
    $path = dirname(__DIR__, 2).'/resources/js/components/ui/select/Select.vue';
    $stylesPath = dirname(__DIR__, 2).'/resources/js/components/ui/select/styles.ts';

    expect(file_exists($path))->toBeTrue();
    expect(file_exists($stylesPath))->toBeTrue();

    $contents = file_get_contents($path);
    $stylesContents = file_get_contents($stylesPath);

    expect($contents)
        ->toContain('clearable?: boolean')
        ->toContain('slot name="selected"')
        ->toContain('slot name="clear-icon"')
        ->toContain('slot name="arrow-icon"')
        ->toContain('handleTriggerKeydown')
        ->toContain('openChange')
        ->toContain('DropdownMenuRadioGroup')
        ->toContain('role="combobox"');

    expect($stylesContents)
        ->toContain('selectLabelVariants')
        ->toContain('selectOptionVariants')
        ->toContain('primary')
        ->toContain('destructive');
});
