<?php

declare(strict_types=1);

it('uses destructive base variants instead of legacy error wrappers in app views', function () {
    $projectRoot = dirname(__DIR__, 2);
    $viewRoots = [
        $projectRoot.'/resources/js/components',
        $projectRoot.'/resources/js/layouts',
        $projectRoot.'/resources/js/pages',
    ];

    foreach ($viewRoots as $viewRoot) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewRoot));

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile()) {
                continue;
            }

            if ($fileInfo->getExtension() !== 'vue') {
                continue;
            }

            $filePath = $fileInfo->getPathname();

            if (str_ends_with($filePath, 'AlertError.vue') || str_ends_with($filePath, 'InputError.vue')) {
                continue;
            }

            $contents = file_get_contents($filePath);

            expect($contents)->not->toContain('AlertError');
            expect($contents)->not->toContain('InputError');
        }
    }
});

it('uses the correct role permission gate in admin layout navigation', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/layouts/AdminLayout.vue');

    expect($contents)->toContain("...(can(adminPermissions.rolesView)\n      ? [{ label: 'Roles', href: adminRolesIndex.url() }]");
    expect($contents)->not->toContain("...(can(adminPermissions.usersView)\n      ? [{ label: 'Roles', href: adminRolesIndex.url() }]");
});

it('uses the canonical wayfinder type surface for admin form contracts', function () {
    $projectRoot = dirname(__DIR__, 2);
    $adminPages = [
        'resources/js/pages/admin/Users/Create.vue',
        'resources/js/pages/admin/Users/Edit.vue',
        'resources/js/pages/admin/Roles/Create.vue',
        'resources/js/pages/admin/Roles/Edit.vue',
        'resources/js/pages/admin/Permissions/Create.vue',
        'resources/js/pages/admin/Permissions/Edit.vue',
    ];

    foreach ($adminPages as $adminPage) {
        $contents = file_get_contents($projectRoot.'/'.$adminPage);

        expect($contents)->toContain("from '@/types/wayfinder-generated'");
        expect($contents)->not->toContain("from '@/types/wayfinder'");
        expect($contents)->not->toContain("App['Forms']");
    }
});

it('uses route modules rather than controller action imports for settings forms', function () {
    $projectRoot = dirname(__DIR__, 2);
    $settingsViews = [
        'resources/js/pages/settings/Profile.vue',
        'resources/js/pages/settings/Password.vue',
        'resources/js/components/DeleteUser.vue',
    ];

    foreach ($settingsViews as $settingsView) {
        $contents = file_get_contents($projectRoot.'/'.$settingsView);

        expect($contents)->not->toContain('@/actions/App/Http/Controllers/Settings/');
        expect($contents)->toContain('.form()');
    }
});

it('breaks shell support components into extracted header, toast, and two-factor units', function () {
    $projectRoot = dirname(__DIR__, 2);
    $headerContents = file_get_contents($projectRoot.'/resources/js/components/AppHeader.vue');
    $toastContents = file_get_contents($projectRoot.'/resources/js/components/AppToasts.vue');
    $twoFactorModalContents = file_get_contents($projectRoot.'/resources/js/components/TwoFactorSetupModal.vue');

    expect($headerContents)->toContain("from '@/components/app-header/AppHeaderDesktopNavigation.vue'");
    expect($headerContents)->toContain("from '@/components/app-header/AppHeaderMobileNavigation.vue'");
    expect($headerContents)->toContain("from '@/components/app-header/AppHeaderUtilityActions.vue'");
    expect($headerContents)->not->toContain("from '@/components/ui/sheet/Sheet.vue'");

    expect($toastContents)->toContain("from '@/components/toasts/AppToastItem.vue'");
    expect($toastContents)->toContain("from '@/composables/useAppToastFeed'");
    expect($toastContents)->not->toContain("from '@inertiajs/vue3'");

    expect($twoFactorModalContents)->toContain("from '@/components/two-factor/TwoFactorSetupStep.vue'");
    expect($twoFactorModalContents)->toContain("from '@/components/two-factor/TwoFactorConfirmationForm.vue'");
    expect($twoFactorModalContents)->not->toContain("from '@inertiajs/vue3'");
});

it('reuses the shared permission editor component across admin permission pages', function () {
    $projectRoot = dirname(__DIR__, 2);
    $permissionPages = [
        'resources/js/pages/admin/Permissions/Create.vue',
        'resources/js/pages/admin/Permissions/Edit.vue',
    ];

    foreach ($permissionPages as $permissionPage) {
        $contents = file_get_contents($projectRoot.'/'.$permissionPage);

        expect($contents)->toContain('PermissionEditorForm');
    }

    $editorContents = file_get_contents($projectRoot.'/resources/js/components/admin/PermissionEditorForm.vue');

    expect($editorContents)->toContain('PermissionGroupSelect');
});

it('reuses the shared frontend permission normalization helper across admin permission forms', function () {
    $projectRoot = dirname(__DIR__, 2);
    $permissionPages = [
        'resources/js/pages/admin/Permissions/Create.vue',
        'resources/js/pages/admin/Permissions/Edit.vue',
    ];

    foreach ($permissionPages as $permissionPage) {
        $contents = file_get_contents($projectRoot.'/'.$permissionPage);

        expect($contents)->toContain("from '@/lib/permissions'");
        expect($contents)->toContain('normalizePermissionName');
        expect($contents)->not->toContain('const extractActionSegment =');
        expect($contents)->not->toContain('const prefixWithGroup =');
    }
});

it('reuses the shared delete confirmation composable across admin destructive actions', function () {
    $projectRoot = dirname(__DIR__, 2);
    $destructivePages = [
        'resources/js/pages/admin/Users/Edit.vue',
        'resources/js/pages/admin/Roles/Edit.vue',
        'resources/js/pages/admin/Permissions/Edit.vue',
        'resources/js/pages/admin/Permissions/Index.vue',
    ];

    foreach ($destructivePages as $destructivePage) {
        $contents = file_get_contents($projectRoot.'/'.$destructivePage);

        expect($contents)->toContain("from '@/composables/useDeleteConfirmation'");
        expect($contents)->toContain('confirmDelete({');
        expect($contents)->not->toContain("if (!confirm('");
    }
});

it('reuses the shared selection-list composable across admin assignment forms', function () {
    $projectRoot = dirname(__DIR__, 2);
    $assignmentPages = [
        'resources/js/pages/admin/Roles/Create.vue',
        'resources/js/pages/admin/Users/Edit.vue',
        'resources/js/pages/admin/Roles/Edit.vue',
    ];

    foreach ($assignmentPages as $assignmentPage) {
        $contents = file_get_contents($projectRoot.'/'.$assignmentPage);

        expect($contents)->toContain("from '@/composables/useSelectionList'");
        expect($contents)->toContain('useSelectionList<');
        expect($contents)->toContain('toggleSelectedValue');
        expect($contents)->not->toContain('const toggleUser =');
        expect($contents)->not->toContain('const toggleRole =');
        expect($contents)->not->toContain('const togglePermission =');
    }
});

it('uses dedicated create and edit pages for admin CRUD forms', function () {
    $projectRoot = dirname(__DIR__, 2);
    $indexPages = [
        'resources/js/pages/admin/Users/Index.vue',
        'resources/js/pages/admin/Roles/Index.vue',
        'resources/js/pages/admin/Permissions/Index.vue',
    ];

    foreach ($indexPages as $indexPage) {
        $contents = file_get_contents($projectRoot.'/'.$indexPage);

        expect($contents)->not->toContain('DialogScrollContent');
        expect($contents)->not->toContain('DialogHeader');
    }
});

it('uses an extracted table-based permission assignment surface in the role management edit page', function () {
    $pageContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Roles/Edit.vue');
    $tableContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/RolePermissionAssignmentTable.vue');
    $shellContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/AssignmentTableCard.vue');

    expect($pageContents)->toContain("from '@/components/admin/RolePermissionAssignmentTable.vue'");
    expect($pageContents)->toContain("from '@/components/admin/RoleDetailsForm.vue'");
    expect($pageContents)->not->toContain('Collapsible');

    expect($tableContents)->toContain("from '@/components/admin/AssignmentTableCard.vue'");
    expect($tableContents)->toContain('<Table');
    expect($tableContents)->toContain('role-permissions-group-filter');
    expect($tableContents)->toContain('role-permissions-search');
    expect($tableContents)->not->toContain('Collapsible');
    expect($shellContents)->toContain('saveLabel');
});

it('uses an extracted permission index table surface in the admin permissions index page', function () {
    $pageContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Permissions/Index.vue');
    $tableContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/PermissionIndexTable.vue');

    expect($pageContents)->toContain("from '@/components/admin/PermissionIndexTable.vue'");
    expect($pageContents)->toContain('@delete-permission="destroyPermission"');
    expect($pageContents)->not->toContain("from '@/composables/usePermissionTable'");

    expect($tableContents)->toContain("from '@/composables/usePermissionTable'");
    expect($tableContents)->toContain('permissions-search');
    expect($tableContents)->toContain('permissions-group-filter');
    expect($tableContents)->toContain('<Table');
});

it('prefills the role name in the role management edit page details form', function () {
    $pageContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Roles/Edit.vue');
    $formContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/RoleDetailsForm.vue');

    expect($pageContents)->toContain('() => props.role.name');
    expect($pageContents)->toContain('{ immediate: true },');
    expect($formContents)->toContain('v-model="form.name"');
    expect($formContents)->toContain(':default-value="form.name"');
});

it('uses extracted details and table-based role assignment surfaces in the user management edit page', function () {
    $pageContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Users/Edit.vue');
    $detailsContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/UserDetailsForm.vue');
    $tableContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/UserRoleAssignmentTable.vue');

    expect($pageContents)->toContain("from '@/components/admin/UserDetailsForm.vue'");
    expect($pageContents)->toContain("from '@/components/admin/UserRoleAssignmentTable.vue'");
    expect($pageContents)->not->toContain('space-y-2');
    expect($pageContents)->not->toContain('rounded-xl border border-black/5');

    expect($detailsContents)->toContain("from '@/components/UserIdentityFields.vue'");
    expect($detailsContents)->toContain('UserIdentityFields');
    expect($tableContents)->toContain("from '@/components/admin/AssignmentTableCard.vue'");
    expect($tableContents)->toContain('<Table');
    expect($tableContents)->toContain('user-roles-search');
});

it('reuses the shared user identity fields across admin and settings user detail forms', function () {
    $projectRoot = dirname(__DIR__, 2);
    $views = [
        'resources/js/components/admin/UserDetailsForm.vue',
        'resources/js/pages/admin/Users/Create.vue',
        'resources/js/pages/settings/Profile.vue',
    ];

    foreach ($views as $view) {
        $contents = file_get_contents($projectRoot.'/'.$view);

        expect($contents)->toContain("from '@/components/UserIdentityFields.vue'");
        expect($contents)->toContain('UserIdentityFields');
    }
});

it('uses partial reload budgets for settings form submissions', function () {
    $projectRoot = dirname(__DIR__, 2);
    $profileContents = file_get_contents($projectRoot.'/resources/js/pages/settings/Profile.vue');
    $passwordContents = file_get_contents($projectRoot.'/resources/js/pages/settings/Password.vue');
    $twoFactorContents = file_get_contents($projectRoot.'/resources/js/pages/settings/TwoFactor.vue');
    $twoFactorConfirmationContents = file_get_contents($projectRoot.'/resources/js/components/two-factor/TwoFactorConfirmationForm.vue');

    expect($profileContents)->toContain("only: ['auth', 'flash']");
    expect($profileContents)->toContain('preserveScroll: true');
    expect($passwordContents)->toContain("only: ['flash']");
    expect($passwordContents)->toContain('preserveScroll: true');
    expect($twoFactorContents)->toContain("only: ['twoFactorEnabled', 'flash']");
    expect($twoFactorConfirmationContents)->toContain("only: ['twoFactorEnabled', 'flash']");
});

it('reuses the shared settings section card across settings surfaces', function () {
    $projectRoot = dirname(__DIR__, 2);
    $settingsViews = [
        'resources/js/pages/settings/Profile.vue',
        'resources/js/pages/settings/Password.vue',
        'resources/js/pages/settings/TwoFactor.vue',
        'resources/js/components/DeleteUser.vue',
    ];

    foreach ($settingsViews as $settingsView) {
        $contents = file_get_contents($projectRoot.'/'.$settingsView);

        expect($contents)->toContain("from '@/components/SettingsSectionCard.vue'");
        expect($contents)->toContain('SettingsSectionCard');
    }
});

it('syncs input default values across inertial page navigations when not using v-model', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/ui/input/Input.vue');

    expect($contents)->toContain('watch(');
    expect($contents)->toContain('() => props.defaultValue');
    expect($contents)->toContain('if (props.modelValue !== undefined)');
    expect($contents)->toContain("modelValue.value = value ?? ''");
});
