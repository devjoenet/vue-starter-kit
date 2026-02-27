<?php

declare(strict_types=1);

it('matches the shared table ui structure', function () {
    $stylesPath = dirname(__DIR__, 2).'/resources/js/components/ui/table/styles.ts';
    $tablePath = dirname(__DIR__, 2).'/resources/js/components/ui/table/Table.vue';
    $headPath = dirname(__DIR__, 2).'/resources/js/components/ui/table/TableHead.vue';
    $rowPath = dirname(__DIR__, 2).'/resources/js/components/ui/table/TableRow.vue';

    expect(file_exists($stylesPath))->toBeTrue();
    expect(file_exists($tablePath))->toBeTrue();
    expect(file_exists($headPath))->toBeTrue();
    expect(file_exists($rowPath))->toBeTrue();

    $stylesContents = file_get_contents($stylesPath);
    $tableContents = file_get_contents($tablePath);

    expect($stylesContents)
        ->toContain('tableWrapperVariants')
        ->toContain('tableHeadVariants')
        ->toContain('tableRowVariants');

    expect($tableContents)
        ->toContain('data-slot="table-wrapper"')
        ->toContain('data-slot="table"')
        ->toContain('tableVariants()');
});

it('uses shared table ui components in admin role, permission, and user index pages', function () {
    $rolesIndex = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Roles/Index.vue');
    $permissionsIndex = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Permissions/Index.vue');
    $usersIndex = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Users/Index.vue');

    expect($rolesIndex)
        ->toContain('from "@/components/ui/table/Table.vue"')
        ->toContain('<Table>')
        ->toContain('<TableHeader>')
        ->toContain('<TableBody>');

    expect($permissionsIndex)
        ->toContain('from "@/components/ui/table/Table.vue"')
        ->toContain('search = ref("")')
        ->toContain('groupFilter = ref("")')
        ->toContain('sortDirections = ref<{')
        ->toContain('permissionRows = computed(')
        ->toContain('sortedRows = computed(')
        ->toContain('toggleSort = (column: "name" | "group")')
        ->toContain('<Table>')
        ->toContain('<TableHeader>')
        ->toContain('<TableBody>');

    expect($usersIndex)
        ->toContain('from "@/components/ui/table/Table.vue"')
        ->toContain('<Table>')
        ->toContain('<TableHeader>')
        ->toContain('<TableBody>');
});
