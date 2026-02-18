<?php

declare(strict_types=1);

it('matches the shared table ui structure', function () {
    $indexPath = dirname(__DIR__, 2).'/resources/js/components/ui/table/index.ts';
    $tablePath = dirname(__DIR__, 2).'/resources/js/components/ui/table/Table.vue';
    $headPath = dirname(__DIR__, 2).'/resources/js/components/ui/table/TableHead.vue';
    $rowPath = dirname(__DIR__, 2).'/resources/js/components/ui/table/TableRow.vue';

    expect(file_exists($indexPath))->toBeTrue();
    expect(file_exists($tablePath))->toBeTrue();
    expect(file_exists($headPath))->toBeTrue();
    expect(file_exists($rowPath))->toBeTrue();

    $indexContents = file_get_contents($indexPath);
    $tableContents = file_get_contents($tablePath);

    expect($indexContents)
        ->toContain('export { default as Table } from "./Table.vue";')
        ->toContain('export { default as TableBody } from "./TableBody.vue";')
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
        ->toContain('from "@/components/ui/table"')
        ->toContain('<Table>')
        ->toContain('<TableHeader>')
        ->toContain('<TableBody>');

    expect($permissionsIndex)
        ->toContain('from "@/components/ui/table"')
        ->toContain('search = ref("")')
        ->toContain('groupFilter = ref("all")')
        ->toContain('sortBy = ref<"name" | "group">("name")')
        ->toContain('permissionRows = computed(')
        ->toContain('sortedRows = computed(')
        ->toContain('toggleSort = (column: "name" | "group")')
        ->toContain('<Table>')
        ->toContain('<TableHeader>')
        ->toContain('<TableBody>');

    expect($usersIndex)
        ->toContain('from "@/components/ui/table"')
        ->toContain('<Table>')
        ->toContain('<TableHeader>')
        ->toContain('<TableBody>');
});
