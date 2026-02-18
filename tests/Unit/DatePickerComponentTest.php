<?php

declare(strict_types=1);

it('matches the enhanced date picker structure', function () {
    $path = dirname(__DIR__, 2).'/resources/js/components/ui/date-picker/DatePicker.vue';
    $indexPath = dirname(__DIR__, 2).'/resources/js/components/ui/date-picker/index.ts';

    expect(file_exists($path))->toBeTrue();
    expect(file_exists($indexPath))->toBeTrue();

    $contents = file_get_contents($path);
    $indexContents = file_get_contents($indexPath);

    expect($contents)
        ->toContain('clearable?: boolean')
        ->toContain('buildCalendarGrid')
        ->toContain('selectToday')
        ->toContain('goToPreviousMonth')
        ->toContain('goToNextMonth')
        ->toContain('DropdownMenuContent')
        ->toContain('role="combobox"')
        ->toContain('weekStartsOn?: 0 | 1');

    expect($indexContents)
        ->toContain('DatePicker')
        ->toContain('datePickerDayVariants')
        ->toContain('selected')
        ->toContain('today');
});
