<?php

declare(strict_types=1);

it('matches the shared table ui structure', function () {
    $stylesPath = dirname(__DIR__, 2).'/resources/js/components/ui/table/variants.ts';
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
        ->toContain('tableRowVariants')
        ->toContain('even:bg-muted/20')
        ->toContain('hover:bg-muted/35')
        ->toContain('dark:even:bg-muted/24')
        ->toContain('dark:hover:bg-muted/40');

    expect($tableContents)
        ->toContain('data-slot="table-wrapper"')
        ->toContain('data-slot="table"')
        ->toContain('tableVariants()');
});

it('uses shared table ui components in admin role, permission, and user index pages', function () {
    $rolesIndex = file_get_contents(dirname(__DIR__, 2).'/Modules/Roles/resources/js/Pages/Index.vue');
    $rolesSurface = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/RoleIndexSurface.vue');
    $permissionsIndex = file_get_contents(dirname(__DIR__, 2).'/Modules/Permissions/resources/js/Pages/Index.vue');
    $permissionsTable = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/PermissionIndexTable.vue');
    $usersIndex = file_get_contents(dirname(__DIR__, 2).'/Modules/Users/resources/js/Pages/Index.vue');
    $usersSurface = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/UserIndexSurface.vue');

    expect($rolesIndex)
        ->toContain("from '@/components/admin/RoleIndexSurface.vue'")
        ->not->toContain("from '@/components/ui/table/Table.vue'");

    expect($rolesSurface)
        ->toContain("from '@/components/ui/table/Table.vue'")
        ->toContain('<Table')
        ->toContain('<TableHeader>')
        ->toContain('<TableBody>');

    expect($permissionsIndex)
        ->toContain("from '@/components/admin/PermissionIndexTable.vue'")
        ->not->toContain("from '@/components/ui/table/Table.vue'");

    expect($permissionsTable)
        ->toContain("from '@/components/ui/table/Table.vue'")
        ->toContain('<Table')
        ->toContain('<TableHeader>')
        ->toContain('<TableBody>');

    expect($usersIndex)
        ->toContain("from '@/components/admin/UserIndexSurface.vue'")
        ->not->toContain("from '@/components/ui/table/Table.vue'");

    expect($usersSurface)
        ->toContain("from '@/components/ui/table/Table.vue'")
        ->toContain('<Table')
        ->toContain('<TableHeader>')
        ->toContain('<TableBody>');
});
