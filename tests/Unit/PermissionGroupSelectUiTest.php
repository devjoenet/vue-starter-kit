<?php

declare(strict_types=1);

it('uses the shared select component instead of a native select', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/PermissionGroupSelect.vue');

    expect($contents)
        ->not->toContain('<select')
        ->toContain('import { Select } from "@/components/ui/select";')
        ->toContain('<Select')
        ->toContain(':options="groupOptions"');
});

it('passes through select styling props', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/PermissionGroupSelect.vue');

    expect($contents)
        ->toContain('variant?: InputVariants["variant"]')
        ->toContain('optionVariant?: SelectOptionVariant')
        ->toContain('optionClass?: HTMLAttributes["class"]')
        ->toContain('contentClass?: HTMLAttributes["class"]')
        ->toContain(':variant="variant"')
        ->toContain(':option-variant="optionVariant"')
        ->toContain(':option-class="optionClass"')
        ->toContain(':content-class="contentClass"')
        ->not->toContain('<DropdownMenu')
        ->not->toContain('<DropdownMenuRadioItem');
});
