<?php

declare(strict_types=1);

it('uses dropdown menu primitives instead of a native select', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/PermissionGroupSelect.vue');

    expect($contents)
        ->not->toContain('<select')
        ->toContain('<DropdownMenu>')
        ->toContain('<DropdownMenuTrigger as-child>')
        ->toContain('<DropdownMenuRadioGroup v-model="modelValue">')
        ->toContain('<DropdownMenuRadioItem');
});

it('supports styling trigger and options via component props', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/PermissionGroupSelect.vue');

    expect($contents)
        ->toContain('triggerAppearance?: ButtonVariants["appearance"]')
        ->toContain('optionVariant?: PermissionGroupOptionVariant')
        ->toContain('optionClass?: HTMLAttributes["class"]')
        ->toContain('contentClass?: HTMLAttributes["class"]')
        ->toContain(':appearance="triggerAppearance"')
        ->toContain('const optionVariantClasses: Record<PermissionGroupOptionVariant, string>')
        ->toContain('const optionStateClasses = computed(() => optionVariantClasses[props.optionVariant])')
        ->toContain('[&>span:first-child]:hidden')
        ->toContain(':class="cn(\'cursor-pointer rounded-md px-3 py-2 pl-3 text-sm font-medium [&>span:first-child]:hidden\', optionStateClasses, optionClass)"')
        ->not->toContain('data-[state=checked]:bg-')
        ->not->toContain('data-[state=checked]:text-');
});
