<?php

declare(strict_types=1);

it('matches the enhanced select variants structure', function () {
    $path = dirname(__DIR__, 2).'/resources/js/components/ui/select/Select.vue';
    $indexPath = dirname(__DIR__, 2).'/resources/js/components/ui/select/index.ts';

    expect(file_exists($path))->toBeTrue();
    expect(file_exists($indexPath))->toBeTrue();

    $contents = file_get_contents($path);
    $indexContents = file_get_contents($indexPath);

    expect($contents)
        ->toContain('clearable?: boolean')
        ->toContain('slot name="selected"')
        ->toContain('slot name="clear-icon"')
        ->toContain('slot name="arrow-icon"')
        ->toContain('handleTriggerKeydown')
        ->toContain('openChange')
        ->toContain('DropdownMenuRadioGroup')
        ->toContain('role="combobox"');

    expect($indexContents)
        ->toContain('selectLabelVariants')
        ->toContain('selectOptionVariants')
        ->toContain('primary')
        ->toContain('destructive');
});
