<?php

declare(strict_types=1);

it('uses the shared select component instead of a native select', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/PermissionGroupSelect.vue');

    expect($contents)
        ->not->toContain('<select')
        ->toContain('import Input from "@/components/ui/input/Input.vue";')
        ->toContain('<Input')
        ->toContain('openSuggestions')
        ->toContain('selectGroup')
        ->toContain('groupSuggestions')
        ->toContain('filteredSuggestions')
        ->not->toContain(':options="groupOptions"');
});

it('passes through input styling props', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/PermissionGroupSelect.vue');

    expect($contents)
        ->toContain('variant?: InputVariants["variant"]')
        ->toContain(':variant="variant"')
        ->toContain(':error="Boolean(error)"')
        ->toContain(':error-text="error"')
        ->toContain(':disabled="disabled"')
        ->not->toContain('<DropdownMenu')
        ->not->toContain('<DropdownMenuRadioItem');
});
