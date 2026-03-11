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
    $toastItemContents = file_get_contents($projectRoot.'/resources/js/components/toasts/AppToastItem.vue');
    $twoFactorModalContents = file_get_contents($projectRoot.'/resources/js/components/TwoFactorSetupModal.vue');

    expect($headerContents)->toContain("from '@/components/app-header/AppHeaderDesktopNavigation.vue'");
    expect($headerContents)->toContain("from '@/components/app-header/AppHeaderMobileNavigation.vue'");
    expect($headerContents)->toContain("from '@/components/app-header/AppHeaderUtilityActions.vue'");
    expect($headerContents)->not->toContain("from '@/components/ui/sheet/Sheet.vue'");

    expect($toastContents)->toContain("from '@/components/toasts/AppToastItem.vue'");
    expect($toastContents)->toContain("from '@/composables/useAppToastFeed'");
    expect($toastContents)->not->toContain("from '@inertiajs/vue3'");
    expect($toastContents)->toContain('w-[min(94vw,18rem)]');

    expect($toastItemContents)->toContain('w-72');
    expect($toastItemContents)->toContain('border-l-[3px]');
    expect($toastItemContents)->toContain('pt-4 pr-4 pb-4 pl-4');
    expect($toastItemContents)->toContain('size-12');
    expect($toastItemContents)->toContain('pt-4 pr-4');
    expect($toastItemContents)->toContain("item.title ? 'mt-4' : 'mt-0.5'");
    expect($toastItemContents)->toContain("item.title ? 'mb-6 opacity-95' : 'mb-4 font-medium'");

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
    ];

    foreach ($destructivePages as $destructivePage) {
        $contents = file_get_contents($projectRoot.'/'.$destructivePage);

        expect($contents)->toContain("from '@/composables/useDeleteConfirmation'");
        expect($contents)->toContain('confirmDelete({');
        expect($contents)->not->toContain('confirm(');
    }

    $dialogContents = file_get_contents($projectRoot.'/resources/js/components/DeleteConfirmationDialog.vue');
    $appContents = file_get_contents($projectRoot.'/resources/js/app.ts');

    expect($dialogContents)->toContain('DialogContent variant="destructive"');
    expect($dialogContents)->toContain('confirmDeleteAction');
    expect($dialogContents)->toContain('closeDeleteConfirmation');
    expect($appContents)->toContain("from './components/DeleteConfirmationDialog.vue'");
    expect($appContents)->toContain('h(DeleteConfirmationDialog)');
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
    expect($pageContents)->toContain("from '@/components/admin/EditPageActionRow.vue'");
    expect($pageContents)->toContain("from '@/composables/useSequentialSave'");
    expect($pageContents)->toContain('quiet_success: true');
    expect($pageContents)->toContain("'Save and Close'");
    expect($pageContents)->toContain("'Close'");
    expect($pageContents)->toContain('router.visit(index.url())');
    expect($pageContents)->not->toContain('lg:grid-cols-2');
    expect($pageContents)->not->toContain('Collapsible');

    expect($tableContents)->toContain("from '@/components/admin/AssignmentTableCard.vue'");
    expect($tableContents)->toContain("from '@/components/admin/AdminIndexHeaderCell.vue'");
    expect($tableContents)->toContain('<Table');
    expect($tableContents)->toContain('label="Group"');
    expect($tableContents)->toContain('label="Permission"');
    expect($tableContents)->toContain('label="Permission Check"');
    expect($tableContents)->not->toContain('role-permissions-search');
    expect($tableContents)->not->toContain('Collapsible');
    expect($tableContents)->not->toContain("from '@/components/ui/select/Select.vue'");
    expect($tableContents)->toContain('hidden text-muted-foreground xl:table-cell');
    expect($shellContents)->not->toContain('saveLabel');
});

it('uses an extracted permission index table surface in the admin permissions index page', function () {
    $pageContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Permissions/Index.vue');
    $tableContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/PermissionIndexTable.vue');

    expect($pageContents)->toContain("from '@/components/admin/PermissionIndexTable.vue'");
    expect($pageContents)->not->toContain("from '@/composables/usePermissionTable'");
    expect($pageContents)->not->toContain("from '@/composables/useDeleteConfirmation'");
    expect($pageContents)->not->toContain('destroyPermission');

    expect($tableContents)->toContain("from '@/components/admin/AdminIndexHeaderCell.vue'");
    expect($tableContents)->toContain('<Table');
    expect($tableContents)->toContain('column="permission"');
    expect($tableContents)->not->toContain('permissions-search');
    expect($tableContents)->not->toContain('permissions-group-filter');
});

it('prefills the role name in the role management edit page details form', function () {
    $pageContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Roles/Edit.vue');
    $createPageContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Roles/Create.vue');
    $formContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/RoleDetailsForm.vue');

    expect($pageContents)->toContain('name: toTitleCase(props.role.name),');
    expect($pageContents)->toContain('name: toTitleCase(roleName),');
    expect($pageContents)->toContain('name: toKebabCase(data.name),');
    expect($pageContents)->toContain('{ immediate: true },');
    expect($createPageContents)->toContain("from '@/lib/utils'");
    expect($createPageContents)->toContain('toTitleCase');
    expect($createPageContents)->toContain('name: toKebabCase(data.name),');
    expect($createPageContents)->toContain('@blur="normalizeRoleNameForDisplay"');
    expect($createPageContents)->not->toContain('lg:grid-cols-2');
    expect($formContents)->toContain('v-model="form.name"');
    expect($formContents)->toContain(':default-value="form.name"');
    expect($formContents)->toContain("from '@/lib/utils'");
    expect($formContents)->toContain('@blur="normalizeRoleNameForDisplay"');
});

it('uses extracted details and table-based role assignment surfaces in the user management edit page', function () {
    $pageContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Users/Edit.vue');
    $detailsContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/UserDetailsForm.vue');
    $tableContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/UserRoleAssignmentTable.vue');

    expect($pageContents)->toContain("from '@/components/admin/UserDetailsForm.vue'");
    expect($pageContents)->toContain("from '@/components/admin/UserRoleAssignmentTable.vue'");
    expect($pageContents)->toContain("from '@/components/admin/EditPageActionRow.vue'");
    expect($pageContents)->toContain("from '@/composables/useSequentialSave'");
    expect($pageContents)->toContain('quiet_success: true');
    expect($pageContents)->toContain("'Save and Close'");
    expect($pageContents)->toContain("'Close'");
    expect($pageContents)->toContain('router.visit(index.url())');
    expect($pageContents)->not->toContain('lg:grid-cols-2');
    expect($pageContents)->not->toContain('space-y-2');
    expect($pageContents)->not->toContain('rounded-xl border border-black/5');

    expect($detailsContents)->toContain("from '@/components/UserIdentityFields.vue'");
    expect($detailsContents)->toContain('UserIdentityFields');
    expect($tableContents)->toContain("from '@/components/admin/AssignmentTableCard.vue'");
    expect($tableContents)->toContain("from '@/components/admin/AdminIndexHeaderCell.vue'");
    expect($tableContents)->toContain('<Table');
    expect($tableContents)->toContain('label="Display Name"');
    expect($tableContents)->toContain('label="Slug"');
    expect($tableContents)->toContain('hidden text-xs font-medium text-muted-foreground italic md:table-cell');
    expect($tableContents)->not->toContain('user-roles-search');
});

it('uses shared index header controls and linked name cells on admin index pages', function () {
    $projectRoot = dirname(__DIR__, 2);
    $indexPages = [
        'resources/js/pages/admin/Users/Index.vue',
        'resources/js/pages/admin/Roles/Index.vue',
    ];

    foreach ($indexPages as $indexPage) {
        $contents = file_get_contents($projectRoot.'/'.$indexPage);

        expect($contents)->toContain("from '@/components/admin/AdminIndexHeaderCell.vue'");
        expect($contents)->not->toContain('Actions</TableHead>');
        expect($contents)->not->toContain('TrashIcon');
        expect($contents)->toContain('font-semibold text-primary underline');
    }

    $permissionPageContents = file_get_contents($projectRoot.'/resources/js/pages/admin/Permissions/Index.vue');
    $headerCellContents = file_get_contents($projectRoot.'/resources/js/components/admin/AdminIndexHeaderCell.vue');
    $permissionTableContents = file_get_contents($projectRoot.'/resources/js/components/admin/PermissionIndexTable.vue');

    expect($permissionPageContents)->toContain('PermissionIndexTable');
    expect($headerCellContents)->toContain('ArrowDownNarrowWideIcon');
    expect($headerCellContents)->toContain('ArrowDownWideNarrowIcon');
    expect($headerCellContents)->toContain('ArrowDownUpIcon');
    expect($headerCellContents)->toContain('FunnelIcon');
    expect($headerCellContents)->toContain('CheckIcon');
    expect($headerCellContents)->toContain('SquareIcon');
    expect($headerCellContents)->toContain('text-sm leading-none font-medium');
    expect($headerCellContents)->toContain('class="relative h-4 w-4 p-0 align-middle"');
    expect($headerCellContents)->toContain('class="h-4 w-4 p-0 align-middle"');
    expect($headerCellContents)->toContain("'size-2'");
    expect($headerCellContents)->toContain("event: 'apply-filters'");
    expect($headerCellContents)->toContain(':appearance="sortDirection === \'none\' ? \'outline\' : \'filled\'"');
    expect($headerCellContents)->toContain(':appearance="hasActiveFilters ? \'filled\' : \'outline\'"');
    expect($headerCellContents)->toContain('@select.prevent');
    expect($headerCellContents)->toContain(':title="filterButtonTitle"');
    expect($headerCellContents)->toContain(':title="sortButtonTitle"');
    expect($headerCellContents)->toContain('text-primary-foreground');
    expect($headerCellContents)->toContain('v-model:open="menuOpen"');
    expect($headerCellContents)->toContain('Apply');
    expect($permissionTableContents)->toContain('label="Permission"');
    expect($permissionTableContents)->toContain('label="Group"');
    expect($permissionTableContents)->toContain('font-semibold text-primary underline');

    expect(
        mb_strpos($permissionTableContents, 'label="Permission"'),
    )->toBeLessThan(mb_strpos($permissionTableContents, 'label="Group"'));
});

it('uses inertia layout props for shared admin and settings breadcrumbs', function () {
    $projectRoot = dirname(__DIR__, 2);
    $pageFiles = [
        'resources/js/pages/admin/Dashboard.vue',
        'resources/js/pages/admin/Users/Index.vue',
        'resources/js/pages/admin/Users/Create.vue',
        'resources/js/pages/admin/Users/Edit.vue',
        'resources/js/pages/admin/Roles/Index.vue',
        'resources/js/pages/admin/Roles/Create.vue',
        'resources/js/pages/admin/Roles/Edit.vue',
        'resources/js/pages/admin/Permissions/Index.vue',
        'resources/js/pages/admin/Permissions/Create.vue',
        'resources/js/pages/admin/Permissions/Edit.vue',
        'resources/js/pages/settings/Profile.vue',
        'resources/js/pages/settings/Password.vue',
        'resources/js/pages/settings/TwoFactor.vue',
        'resources/js/pages/settings/Appearance.vue',
    ];

    $layoutContents = file_get_contents($projectRoot.'/resources/js/layouts/AppLayout.vue');

    expect($layoutContents)->toContain('useLayoutProps');
    expect($layoutContents)->toContain('breadcrumbs: []');

    foreach ($pageFiles as $pageFile) {
        $contents = file_get_contents($projectRoot.'/'.$pageFile);

        expect($contents)->toContain('setLayoutProps({');
        expect($contents)->not->toContain('layout: (_: unknown, page: unknown) =>');
    }
});

it('assigns meaningful dom ids to every page surface', function () {
    $projectRoot = dirname(__DIR__, 2);
    $pageIdExpectations = [
        'resources/js/pages/Welcome.vue' => [
            'id="welcome-page"',
            'id="welcome-page-hero"',
            'id="welcome-page-visual"',
        ],
        'resources/js/pages/ErrorPage.vue' => [
            'id="error-page"',
            'id="error-page-card"',
            'id="error-page-actions"',
        ],
        'resources/js/pages/admin/Dashboard.vue' => [
            'id="admin-dashboard-page"',
            'id="admin-dashboard-quick-links"',
            'id="admin-dashboard-main-panel"',
        ],
        'resources/js/pages/admin/Users/Index.vue' => [
            'id="admin-users-index-page"',
            'id="admin-users-index-page-header"',
            'id="admin-users-index-table"',
        ],
        'resources/js/pages/admin/Users/Create.vue' => [
            'id="admin-users-create-page"',
            'id="admin-users-create-form"',
            'id="admin-users-create-submit-button"',
        ],
        'resources/js/pages/admin/Users/Edit.vue' => [
            'id="admin-users-edit-page"',
            'id="admin-users-edit-details-card"',
            'id="admin-users-edit-actions"',
        ],
        'resources/js/pages/admin/Roles/Index.vue' => [
            'id="admin-roles-index-page"',
            'id="admin-roles-index-page-header"',
            'id="admin-roles-index-table"',
        ],
        'resources/js/pages/admin/Roles/Create.vue' => [
            'id="admin-roles-create-page"',
            'id="admin-roles-create-form"',
            'id="admin-roles-create-users-card"',
        ],
        'resources/js/pages/admin/Roles/Edit.vue' => [
            'id="admin-roles-edit-page"',
            'id="admin-roles-edit-permissions-card"',
            'id="admin-roles-edit-actions"',
        ],
        'resources/js/pages/admin/Permissions/Index.vue' => [
            'id="admin-permissions-index-page"',
            'id="admin-permissions-index-page-header"',
            'id="admin-permissions-index-table-card"',
        ],
        'resources/js/pages/admin/Permissions/Create.vue' => [
            'id="admin-permissions-create-page"',
            'id="admin-permissions-create-page-header"',
            'id="admin-permissions-create-form-card"',
        ],
        'resources/js/pages/admin/Permissions/Edit.vue' => [
            'id="admin-permissions-edit-page"',
            'id="admin-permissions-edit-page-header"',
            'id="admin-permissions-edit-form-card"',
        ],
        'resources/js/pages/settings/Profile.vue' => [
            'id="settings-profile-page"',
            'id="settings-profile-information-form"',
            'id="settings-profile-delete-account-card"',
        ],
        'resources/js/pages/settings/Password.vue' => [
            'id="settings-password-page"',
            'id="settings-password-form"',
            'id="settings-password-save-button"',
        ],
        'resources/js/pages/settings/TwoFactor.vue' => [
            'id="settings-two-factor-page"',
            'id="settings-two-factor-card"',
            'id="settings-two-factor-setup-modal"',
        ],
        'resources/js/pages/settings/Appearance.vue' => [
            'id="settings-appearance-page"',
            'id="settings-appearance-card"',
            'id="settings-appearance-tabs"',
        ],
        'resources/js/pages/auth/Login.vue' => [
            'id="auth-login-page"',
            'id="auth-login-form"',
            'id="auth-login-submit-button"',
        ],
        'resources/js/pages/auth/Register.vue' => [
            'id="auth-register-page"',
            'id="auth-register-form"',
            'id="auth-register-submit-button"',
        ],
        'resources/js/pages/auth/ForgotPassword.vue' => [
            'id="auth-forgot-password-page"',
            'id="auth-forgot-password-form"',
            'id="auth-forgot-password-submit-button"',
        ],
        'resources/js/pages/auth/ResetPassword.vue' => [
            'id="auth-reset-password-page"',
            'id="auth-reset-password-form"',
            'id="auth-reset-password-submit-button"',
        ],
        'resources/js/pages/auth/ConfirmPassword.vue' => [
            'id="auth-confirm-password-page"',
            'id="auth-confirm-password-form"',
            'id="auth-confirm-password-submit-button"',
        ],
        'resources/js/pages/auth/TwoFactorChallenge.vue' => [
            'id="auth-two-factor-challenge-page"',
            'id="auth-two-factor-code-form"',
            'id="auth-two-factor-recovery-form"',
        ],
        'resources/js/pages/auth/VerifyEmail.vue' => [
            'id="auth-verify-email-page"',
            'id="auth-verify-email-form"',
            'id="auth-verify-email-submit-button"',
        ],
    ];

    foreach ($pageIdExpectations as $pageFile => $expectedIds) {
        $contents = file_get_contents($projectRoot.'/'.$pageFile);

        foreach ($expectedIds as $expectedId) {
            expect($contents)->toContain($expectedId);
        }
    }
});

it('keeps browser globals behind ssr-safe guards in shared composables', function () {
    $currentUrlContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/composables/useCurrentUrl.ts');
    $toastContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/composables/useToast.ts');
    $sidebarProviderContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/ui/sidebar/SidebarProvider.vue');

    expect($currentUrlContents)->not->toContain('window?.location.origin');
    expect($currentUrlContents)->toContain("new URL(pageUrl, 'http://localhost').pathname");

    expect($toastContents)->toContain("if (typeof window === 'undefined')");
    expect($toastContents)->toContain('window.requestAnimationFrame(callback)');

    expect($sidebarProviderContents)->toContain("if (typeof document !== 'undefined')");
});

it('cycles shared admin index sorting through asc, desc, and none', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/composables/useAdminIndexTableQuery.ts');

    expect($contents)->toContain('if (currentQuery.value.sort !== column)');
    expect($contents)->toContain("direction: 'asc'");
    expect($contents)->toContain("direction: 'desc'");
    expect($contents)->toContain('sort: undefined');
    expect($contents)->toContain('direction: undefined');
});

it('renders dashboard metric cards before placeholder sections', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Dashboard.vue');

    expect(
        mb_strpos($contents, 'id="admin-dashboard-quick-links"'),
    )->toBeLessThan(mb_strpos($contents, 'id="admin-dashboard-highlight-grid"'));
});

it('keeps permission edit delete actions in the shared form footer', function () {
    $pageContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Permissions/Edit.vue');
    $formContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/PermissionEditorForm.vue');

    expect($pageContents)->toContain(':can-delete="canDelete"');
    expect($pageContents)->toContain('@delete="destroyPermission"');
    expect($pageContents)->toContain("'Save and Close'");
    expect($pageContents)->toContain("'Close'");
    expect($pageContents)->toContain('quiet_success: true');
    expect($pageContents)->toContain('router.visit(index.url())');
    expect($pageContents)->not->toContain("from '@/components/ui/button/Button.vue'");

    expect($formContents)->toContain("event: 'delete'");
    expect($formContents)->toContain('variant="destructive"');
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
