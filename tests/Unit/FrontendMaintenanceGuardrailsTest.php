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

    expect($contents)->toContain("can(adminPermissions.rolesView) ? [{ label: 'Roles', href: adminRolesIndex.url() }] : []");
    expect($contents)->not->toContain("can(adminPermissions.usersView) ? [{ label: 'Roles', href: adminRolesIndex.url() }] : []");
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
    expect($toastItemContents)->toContain('rounded-[1.375rem]');
    expect($toastItemContents)->toContain('pt-4 pr-4 pb-4 pl-4');
    expect($toastItemContents)->toContain('size-10');
    expect($toastItemContents)->toContain('toneBadgeClasses');
    expect($toastItemContents)->toContain('toneLabelMap');
    expect($toastItemContents)->toContain('rounded-full');

    expect($twoFactorModalContents)->toContain("from '@/components/two-factor/TwoFactorSetupStep.vue'");
    expect($twoFactorModalContents)->toContain("from '@/components/two-factor/TwoFactorConfirmationForm.vue'");
    expect($twoFactorModalContents)->not->toContain("from '@inertiajs/vue3'");
});

it('keeps request-failure handling centralized for stale sessions and partial backend failures', function () {
    $projectRoot = dirname(__DIR__, 2);
    $failureUtilityContents = file_get_contents($projectRoot.'/resources/js/lib/request-failures.ts');
    $toastFeedContents = file_get_contents($projectRoot.'/resources/js/composables/useAppToastFeed.ts');
    $twoFactorContents = file_get_contents($projectRoot.'/resources/js/composables/useTwoFactorAuth.ts');

    expect($failureUtilityContents)->toContain('status === 419');
    expect($failureUtilityContents)->toContain('Ref:');
    expect($toastFeedContents)->toContain("router.on('httpException'");
    expect($toastFeedContents)->toContain("router.on('networkError'");
    expect($toastFeedContents)->toContain("from '@/lib/request-failures'");
    expect($toastFeedContents)->toContain('return false;');
    expect($twoFactorContents)->toContain("from '@/lib/request-failures'");
    expect($twoFactorContents)->toContain("'X-Requested-With': 'XMLHttpRequest'");
});

it('keeps frontend asset budgets enforced in the build pipeline', function () {
    $projectRoot = dirname(__DIR__, 2);
    $packageJson = file_get_contents($projectRoot.'/package.json');
    $viteConfigContents = file_get_contents($projectRoot.'/vite.config.ts');
    $budgetScriptContents = file_get_contents($projectRoot.'/scripts/check-frontend-budgets.mjs');

    expect($packageJson)->toContain('"budget:frontend"');
    expect($packageJson)->toContain('npm run budget:frontend');
    expect($viteConfigContents)->toContain('chunkSizeWarningLimit: 340');
    expect($budgetScriptContents)->toContain('resources/js/app.ts');
    expect($budgetScriptContents)->toContain('resources/js/pages/admin/Dashboard.vue');
    expect($budgetScriptContents)->toContain('Frontend asset budgets passed.');
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
    expect($pageContents)->toContain('save-label="Save and Close"');
    expect($pageContents)->toContain('close-label="Close"');
    expect($pageContents)->toContain('router.visit(index.url())');
    expect($pageContents)->toContain('id="admin-roles-edit-sections" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start"');
    expect($pageContents)->toContain('id="admin-roles-edit-permissions-card" class="motion-step xl:col-span-2"');
    expect($pageContents)->toContain('<aside class="motion-step');
    expect($pageContents)->toContain('xl:col-start-2');
    expect($pageContents)->toContain('xl:row-start-1');
    expect($pageContents)->toContain('xl:sticky');
    expect($pageContents)->toContain('xl:top-6');
    expect($pageContents)->toContain('xl:self-start');
    expect($pageContents)->toContain('id="admin-roles-edit-actions"');
    expect($pageContents)->toContain('class="xl:ml-auto xl:w-full"');
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
    expect($tableContents)->toContain("column: 'permission'");
    expect($tableContents)->toContain("label: 'Permission'");
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
    expect($createPageContents)->toContain("from '@/components/admin/RoleDetailsForm.vue'");
    expect($createPageContents)->toContain('name: toKebabCase(data.name),');
    expect($createPageContents)->toContain('name-id="create-role-name"');
    expect($createPageContents)->not->toContain('lg:grid-cols-2');
    expect($formContents)->toContain('v-model="form.name"');
    expect($formContents)->toContain(':default-value="form.name"');
    expect($formContents)->toContain("from '@/lib/utils'");
    expect($formContents)->toContain('@blur="normalizeRoleNameForDisplay"');
});

it('uses a consistent fixed-width side rail across admin editor pages', function () {
    $projectRoot = dirname(__DIR__, 2);
    $editorPages = [
        'resources/js/pages/admin/Users/Create.vue',
        'resources/js/pages/admin/Users/Edit.vue',
        'resources/js/pages/admin/Roles/Create.vue',
        'resources/js/pages/admin/Roles/Edit.vue',
        'resources/js/pages/admin/Permissions/Create.vue',
        'resources/js/pages/admin/Permissions/Edit.vue',
    ];

    foreach ($editorPages as $editorPage) {
        $contents = file_get_contents($projectRoot.'/'.$editorPage);

        expect($contents)->toContain('xl:grid-cols-[minmax(0,1fr)_18rem]');
        expect($contents)->toContain('xl:items-start');
        expect($contents)->not->toContain('minmax(18rem,0.82fr)');
    }
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
    expect($pageContents)->toContain('save-label="Save and Close"');
    expect($pageContents)->toContain('close-label="Close"');
    expect($pageContents)->toContain('router.visit(index.url())');
    expect($pageContents)->toContain('id="admin-users-edit-sections" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start"');
    expect($pageContents)->toContain('id="admin-users-edit-roles-card" class="motion-step xl:col-span-2"');
    expect($pageContents)->toContain('<aside class="motion-step');
    expect($pageContents)->toContain('xl:col-start-2');
    expect($pageContents)->toContain('xl:row-start-1');
    expect($pageContents)->toContain('xl:sticky');
    expect($pageContents)->toContain('xl:top-6');
    expect($pageContents)->toContain('xl:self-start');
    expect($pageContents)->toContain('class="xl:ml-auto xl:w-full"');
    expect($pageContents)->not->toContain('lg:grid-cols-2');
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

it('uses a full-width initial assignee table in the role create page', function () {
    $pageContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Roles/Create.vue');

    expect($pageContents)->toContain("from '@/components/admin/RoleDetailsForm.vue'");
    expect($pageContents)->toContain("from '@/components/admin/AssignmentTableCard.vue'");
    expect($pageContents)->toContain("from '@/components/admin/EditPageActionRow.vue'");
    expect($pageContents)->toContain('id="admin-roles-create-form" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_18rem] xl:items-start"');
    expect($pageContents)->toContain('id="admin-roles-create-sections" class="contents"');
    expect($pageContents)->toContain('id="admin-roles-create-users-card"');
    expect($pageContents)->toContain('class="motion-step xl:col-span-2"');
    expect($pageContents)->toContain('class="space-y-4 xl:col-start-2 xl:row-start-1 xl:self-start"');
    expect($pageContents)->toContain('id="admin-roles-create-actions"');
    expect($pageContents)->toContain('class="motion-step xl:w-full"');
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
    expect($headerCellContents)->toContain('ArrowUpDownIcon');
    expect($headerCellContents)->toContain('FunnelIcon');
    expect($headerCellContents)->toContain('CheckIcon');
    expect($headerCellContents)->toContain('SquareIcon');
    expect($headerCellContents)->toContain("as?: 'table' | 'toolbar'");
    expect($headerCellContents)->toContain('text-sm leading-none font-medium');
    expect($headerCellContents)->toContain('size="iconSm"');
    expect($headerCellContents)->toContain('rounded="full"');
    expect($headerCellContents)->toContain("props.as === 'toolbar'");
    expect($headerCellContents)->toContain('class="size-3"');
    expect($headerCellContents)->toContain("event: 'apply-filters'");
    expect($headerCellContents)->toContain('@select.prevent');
    expect($headerCellContents)->toContain(':title="filterButtonTitle"');
    expect($headerCellContents)->toContain(':title="sortButtonTitle"');
    expect($headerCellContents)->toContain('v-model:open="menuOpen"');
    expect($headerCellContents)->toContain('Apply');
    expect($permissionTableContents)->toContain("label: 'Permission'");
    expect($permissionTableContents)->toContain("label: 'Group'");
    expect($permissionTableContents)->toContain('font-semibold text-primary underline');

    expect(
        mb_strpos($permissionTableContents, "label: 'Permission'"),
    )->toBeLessThan(mb_strpos($permissionTableContents, "label: 'Group'"));
});

it('keeps auth forms in native DOM focus order without positive tabindex values', function () {
    $projectRoot = dirname(__DIR__, 2);
    $loginContents = file_get_contents($projectRoot.'/resources/js/pages/auth/Login.vue');
    $registerContents = file_get_contents($projectRoot.'/resources/js/pages/auth/Register.vue');
    $textLinkContents = file_get_contents($projectRoot.'/resources/js/components/TextLink.vue');

    expect($loginContents)->not->toContain(':tabindex=');
    expect($loginContents)->not->toContain('tabindex="');
    expect($registerContents)->not->toContain(':tabindex=');
    expect($registerContents)->not->toContain('tabindex="');
    expect($textLinkContents)->not->toContain('tabindex?:');
    expect($textLinkContents)->not->toContain(':tabindex="tabindex"');
});

it('keeps the Southeast Code mark visible in the welcome-page header', function () {
    $welcomeContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/Welcome.vue');
    $authLayoutContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/layouts/auth/AuthSimpleLayout.vue');
    $brandLockupContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/SurfaceBrandLockup.vue');

    expect($welcomeContents)->toContain("from '@/components/SurfaceBrandLockup.vue'");
    expect($welcomeContents)->toContain('id="welcome-page-header"');
    expect($welcomeContents)->toContain('id="welcome-page-brand"');
    expect($welcomeContents)->toContain('<SurfaceBrandLockup');
    expect($welcomeContents)->toContain('<h1');

    expect($authLayoutContents)->toContain("from '@/components/SurfaceBrandLockup.vue'");
    expect($authLayoutContents)->toContain('<SurfaceBrandLockup');
    expect($authLayoutContents)->toContain('auth-page-theme');
    expect($authLayoutContents)->toContain('surface-auth-stage');
    expect($authLayoutContents)->toContain('surface-auth-callout');

    expect($brandLockupContents)->toContain("from '@/components/AppLogoIcon.vue'");
    expect($brandLockupContents)->toContain('gaOutlineClass="fill-primary"');
    expect($brandLockupContents)->toContain('codeIconClass="fill-secondary"');
    expect($brandLockupContents)->toContain('whitespace-nowrap');
    expect($brandLockupContents)->toContain('Southeast Code');
});

it('anchors the welcome page around the shared hero illustration and custom systems positioning', function () {
    $projectRoot = dirname(__DIR__, 2);
    $welcomeContents = file_get_contents($projectRoot.'/resources/js/pages/Welcome.vue');
    $cssContents = file_get_contents($projectRoot.'/resources/css/app.css');

    expect($welcomeContents)->toContain("from '@/components/WelcomeHeroIllustration.vue'");
    expect($welcomeContents)->toContain('<WelcomeHeroIllustration');
    expect($welcomeContents)->toContain('Custom systems that help real teams operate <span');
    expect($welcomeContents)->toContain('welcome-page-theme');
    expect($welcomeContents)->toContain('id="welcome-page-hero"');
    expect($welcomeContents)->toContain('id="welcome-page-visual"');
    expect($welcomeContents)->toContain('id="welcome-page-build-targets"');
    expect($welcomeContents)->toContain('One starter. Many surfaces.');
    expect($welcomeContents)->toContain('From one starting point');
    expect($welcomeContents)->toContain('relative z-[2] max-w-140');
    expect($welcomeContents)->not->toContain('welcome-copy-surface');
    expect($welcomeContents)->not->toContain('welcome-hero-frame');
    expect($welcomeContents)->not->toContain('welcome-hero-chrome');

    expect($cssContents)->toContain('.welcome-page-theme');
    expect($cssContents)->toContain('.welcome-hero-shell');
    expect($cssContents)->toContain('.welcome-hero-media');
    expect($cssContents)->toContain('.welcome-foundation-shell');
    expect($cssContents)->not->toContain('.welcome-copy-surface');
});

it('keeps app css focused on shared theme and reusable surface primitives', function () {
    $cssContents = file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');

    expect($cssContents)->toContain('--primary-100');
    expect($cssContents)->toContain('--success: var(--success-500);');
    expect($cssContents)->toContain('--success-foreground: var(--success-900);');
    expect($cssContents)->toContain('--color-success-500: var(--success-500);');
    expect($cssContents)->toContain('--color-primary-600: var(--primary-600);');
    expect($cssContents)->not->toContain('.surface-public-hero');
    expect($cssContents)->not->toContain('.surface-public-panel');
    expect($cssContents)->not->toContain('.surface-auth-panel');
    expect($cssContents)->not->toContain('.surface-auth-grid');
    expect($cssContents)->not->toContain('.surface-auth-copy');
    expect($cssContents)->not->toContain('.surface-auth-form-slot');
    expect($cssContents)->not->toContain('.surface-auth-trust-point');
    expect($cssContents)->not->toContain('.welcome-proof-kicker');
    expect($cssContents)->not->toContain('.welcome-hero-grid');
    expect($cssContents)->not->toContain('.welcome-target-row');
});

it('uses the shared settings workspace shell for settings navigation and headings', function () {
    $settingsLayoutContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/layouts/settings/Layout.vue');

    expect($settingsLayoutContents)->toContain('surface-settings-shell');
    expect($settingsLayoutContents)->toContain('surface-settings-nav');
    expect($settingsLayoutContents)->toContain('activeNavItem.heading');
    expect($settingsLayoutContents)->toContain('motion-sheen');
    expect($settingsLayoutContents)->toContain('motion-step');
});

it('keeps the shared motion primitives and reduced-motion safeguards in the frontend system', function () {
    $cssContents = file_get_contents(dirname(__DIR__, 2).'/resources/css/app.css');
    $buttonStylesContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/ui/button/variants.ts');
    $sidebarStylesContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/ui/sidebar/variants.ts');

    expect($cssContents)->toContain('--motion-ease-out-quart');
    expect($cssContents)->toContain('.motion-stage');
    expect($cssContents)->toContain('.motion-step');
    expect($cssContents)->toContain('.motion-interactive-raise');
    expect($cssContents)->toContain('.motion-sheen');
    expect($cssContents)->toContain('@media (prefers-reduced-motion: reduce)');

    expect($buttonStylesContents)->toContain('motion-safe:hover:-translate-y-px');
    expect($sidebarStylesContents)->toContain('motion-interactive-slide');
});

it('uses shared motion choreography across public, auth, dashboard, and admin workspace surfaces', function () {
    $projectRoot = dirname(__DIR__, 2);
    $welcomeContents = file_get_contents($projectRoot.'/resources/js/pages/Welcome.vue');
    $authLayoutContents = file_get_contents($projectRoot.'/resources/js/layouts/auth/AuthSimpleLayout.vue');
    $dashboardContents = file_get_contents($projectRoot.'/resources/js/pages/admin/Dashboard.vue');
    $usersIndexContents = file_get_contents($projectRoot.'/resources/js/pages/admin/Users/Index.vue');
    $rolesIndexContents = file_get_contents($projectRoot.'/resources/js/pages/admin/Roles/Index.vue');
    $permissionsIndexContents = file_get_contents($projectRoot.'/resources/js/pages/admin/Permissions/Index.vue');

    expect($welcomeContents)->toContain('motion-stage');
    expect($authLayoutContents)->toContain('motion-stage');
    expect($dashboardContents)->toContain('motion-stage');
    expect($usersIndexContents)->toContain('motion-stage');
    expect($rolesIndexContents)->toContain('motion-stage');
    expect($permissionsIndexContents)->toContain('motion-stage');
});

it('uses shared breadcrumb helpers for admin and settings pages', function () {
    $projectRoot = dirname(__DIR__, 2);
    $dashboardPageFile = 'resources/js/pages/admin/Dashboard.vue';
    $adminPageFiles = [
        'resources/js/pages/admin/Users/Index.vue',
        'resources/js/pages/admin/Users/Create.vue',
        'resources/js/pages/admin/Users/Edit.vue',
        'resources/js/pages/admin/Roles/Index.vue',
        'resources/js/pages/admin/Roles/Create.vue',
        'resources/js/pages/admin/Roles/Edit.vue',
        'resources/js/pages/admin/Permissions/Index.vue',
        'resources/js/pages/admin/Permissions/Create.vue',
        'resources/js/pages/admin/Permissions/Edit.vue',
    ];
    $settingsPageFiles = [
        'resources/js/pages/settings/Profile.vue',
        'resources/js/pages/settings/Password.vue',
        'resources/js/pages/settings/TwoFactor.vue',
        'resources/js/pages/settings/Appearance.vue',
    ];

    $layoutContents = file_get_contents($projectRoot.'/resources/js/layouts/AppLayout.vue');

    expect($layoutContents)->not->toContain('useLayoutProps');
    expect($layoutContents)->toContain('breadcrumbs: () => []');

    $dashboardContents = file_get_contents($projectRoot.'/'.$dashboardPageFile);

    expect($dashboardContents)->toContain('setBreadcrumbs(');
    expect($dashboardContents)->not->toContain('setLayoutProps({');
    expect($dashboardContents)->not->toContain('layout: (_: unknown, page: unknown) =>');

    foreach ($adminPageFiles as $pageFile) {
        $contents = file_get_contents($projectRoot.'/'.$pageFile);

        expect($contents)->toContain('setAdminBreadcrumbs(');
        expect($contents)->not->toContain('setLayoutProps({');
        expect($contents)->not->toContain('layout: (_: unknown, page: unknown) =>');
    }

    foreach ($settingsPageFiles as $pageFile) {
        $contents = file_get_contents($projectRoot.'/'.$pageFile);

        expect($contents)->toContain('setBreadcrumbs(');
        expect($contents)->not->toContain('setLayoutProps({');
        expect($contents)->not->toContain('layout: (_: unknown, page: unknown) =>');
    }
});

it('keeps dashboard, admin, and settings pages on the shared sidebar-based layouts', function () {
    $projectRoot = dirname(__DIR__, 2);
    $dashboardContents = file_get_contents($projectRoot.'/resources/js/pages/admin/Dashboard.vue');
    $adminPageFiles = [
        'resources/js/pages/admin/Users/Index.vue',
        'resources/js/pages/admin/Users/Create.vue',
        'resources/js/pages/admin/Users/Edit.vue',
        'resources/js/pages/admin/Roles/Index.vue',
        'resources/js/pages/admin/Roles/Create.vue',
        'resources/js/pages/admin/Roles/Edit.vue',
        'resources/js/pages/admin/Permissions/Index.vue',
        'resources/js/pages/admin/Permissions/Create.vue',
        'resources/js/pages/admin/Permissions/Edit.vue',
    ];
    $settingsPageFiles = [
        'resources/js/pages/settings/Profile.vue',
        'resources/js/pages/settings/Password.vue',
        'resources/js/pages/settings/TwoFactor.vue',
        'resources/js/pages/settings/Appearance.vue',
    ];

    expect($dashboardContents)->toContain('layout: adminPageLayout');
    expect($dashboardContents)->not->toContain('layout: AuthLayout');

    foreach ($adminPageFiles as $pageFile) {
        $contents = file_get_contents($projectRoot.'/'.$pageFile);

        expect($contents)->toContain('layout: adminPageLayout');
    }

    foreach ($settingsPageFiles as $pageFile) {
        $contents = file_get_contents($projectRoot.'/'.$pageFile);

        expect($contents)->toContain('layout: settingsPageLayout');
        expect($contents)->not->toContain('layout: adminPageLayout');
    }
});

it('assigns meaningful dom ids to every page surface', function () {
    $projectRoot = dirname(__DIR__, 2);
    $pageIdExpectations = [
        'resources/js/pages/Welcome.vue' => [
            'id="welcome-page"',
            'id="welcome-page-hero"',
            'id="welcome-page-proof-panel"',
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

it('keeps the admin dashboard free of decorative placeholder surfaces', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Dashboard.vue');
    $quickLinksContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/AdminQuickLinks.vue');

    expect(mb_substr_count($contents, '<h1'))->toBe(1);
    expect($contents)->toContain('id="admin-dashboard-hero"');
    expect($contents)->toContain('id="admin-dashboard-heading"');
    expect($contents)->toContain('id="admin-dashboard-command-stage"');
    expect($contents)->toContain('id="admin-dashboard-quick-links"');
    expect($contents)->toContain('id="admin-dashboard-support-band"');
    expect($contents)->toContain('id="admin-dashboard-main-panel"');
    expect($contents)->toContain('id="admin-dashboard-focus-panel"');
    expect($contents)->toContain('id="admin-dashboard-readiness-panel"');
    expect($contents)->toContain('Keep access visible, tidy, and ready for handoff.');
    expect($contents)->not->toContain('PlaceholderPattern');
    expect($contents)->not->toContain('id="admin-dashboard-highlight-grid"');
    expect($quickLinksContents)->toContain('id="admin-dashboard-quick-links-nav"');
    expect($quickLinksContents)->toContain('admin-dashboard-link-${item.id}');
    expect($quickLinksContents)->toContain("id: 'users'");
    expect($quickLinksContents)->toContain("id: 'roles'");
    expect($quickLinksContents)->toContain("id: 'permissions'");
    expect($quickLinksContents)->not->toContain('md:grid-cols-3');
    expect($quickLinksContents)->not->toContain('Available now');
});

it('keeps high-identity surfaces on semantic landmark primitives', function () {
    $projectRoot = dirname(__DIR__, 2);
    $welcomeContents = file_get_contents($projectRoot.'/resources/js/pages/Welcome.vue');
    $authLayoutContents = file_get_contents($projectRoot.'/resources/js/layouts/auth/AuthSimpleLayout.vue');
    $settingsLayoutContents = file_get_contents($projectRoot.'/resources/js/layouts/settings/Layout.vue');
    $appContentContents = file_get_contents($projectRoot.'/resources/js/components/AppContent.vue');

    expect($welcomeContents)->toContain('<header id="welcome-page-header"');
    expect($welcomeContents)->toContain('<main');
    expect($welcomeContents)->toContain('aria-labelledby="welcome-page-heading"');
    expect($welcomeContents)->toContain('id="welcome-page-heading"');

    expect($authLayoutContents)->toContain('<header id="auth-page-header"');
    expect($authLayoutContents)->toContain('<main');
    expect($authLayoutContents)->toContain('aria-labelledby="auth-page-title"');
    expect($authLayoutContents)->toContain('id="auth-page-title"');

    expect($settingsLayoutContents)->toContain('<header id="settings-layout-header"');
    expect($settingsLayoutContents)->toContain('aria-label="Settings"');

    expect($appContentContents)->toContain('<main');
});

it('keeps the admin dashboard aligned to the welcome shell hierarchy', function () {
    $projectRoot = dirname(__DIR__, 2);
    $dashboardContents = file_get_contents($projectRoot.'/resources/js/pages/admin/Dashboard.vue');
    $quickLinksContents = file_get_contents($projectRoot.'/resources/js/components/admin/AdminQuickLinks.vue');
    $welcomeContents = file_get_contents($projectRoot.'/resources/js/pages/Welcome.vue');

    expect($welcomeContents)->toContain('id="welcome-page-build-targets"');
    expect($dashboardContents)->toContain('id="admin-dashboard-heading"');
    expect($dashboardContents)->toContain('id="admin-dashboard-command-stage"');
    expect($dashboardContents)->toContain('id="admin-dashboard-support-band"');
    expect($dashboardContents)->toContain('aria-labelledby="admin-dashboard-support-heading"');
    expect($dashboardContents)->not->toContain('id="admin-dashboard-highlight-grid"');
    expect($dashboardContents)->not->toContain('Analytics');
    expect($quickLinksContents)->toContain('aria-labelledby="admin-dashboard-quick-links-heading"');
    expect($quickLinksContents)->toContain('Command strip');
    expect($quickLinksContents)->not->toContain('metrics');

    expect(mb_strpos($dashboardContents, 'id="admin-dashboard-command-stage"'))->toBeGreaterThan(mb_strpos($dashboardContents, 'id="admin-dashboard-main-panel"'));
    expect(mb_strpos($dashboardContents, 'id="admin-dashboard-quick-links"'))->toBeGreaterThan(mb_strpos($dashboardContents, 'id="admin-dashboard-command-stage"'));
    expect(mb_strpos($dashboardContents, 'id="admin-dashboard-support-band"'))->toBeGreaterThan(mb_strpos($dashboardContents, 'id="admin-dashboard-quick-links"'));
});

it('keeps permission edit delete actions in the shared form footer', function () {
    $pageContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/pages/admin/Permissions/Edit.vue');
    $formContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/PermissionEditorForm.vue');

    expect($pageContents)->toContain(':can-delete="canDelete"');
    expect($pageContents)->toContain('@delete="destroyPermission"');
    expect($pageContents)->toContain('submit-label="Save and Close"');
    expect($pageContents)->toContain('close-label="Close"');
    expect($pageContents)->toContain('quiet_success: true');
    expect($pageContents)->toContain('router.visit(index.url())');
    expect($pageContents)->not->toContain("from '@/components/ui/button/Button.vue'");

    expect($formContents)->toContain("event: 'delete'");
    expect($formContents)->toContain("from '@/components/admin/EditPageActionRow.vue'");
    expect($formContents)->toContain('@delete="$emit(\'delete\')"');
});

it('reuses the shared user identity fields across admin and settings user detail forms', function () {
    $projectRoot = dirname(__DIR__, 2);
    $views = [
        'resources/js/components/admin/UserDetailsForm.vue',
        'resources/js/pages/admin/Users/Create.vue',
        'resources/js/pages/settings/Profile.vue',
    ];

    $detailsContents = file_get_contents($projectRoot.'/resources/js/components/admin/UserDetailsForm.vue');
    $createPageContents = file_get_contents($projectRoot.'/resources/js/pages/admin/Users/Create.vue');
    $settingsContents = file_get_contents($projectRoot.'/resources/js/pages/settings/Profile.vue');

    expect($detailsContents)->toContain("from '@/components/UserIdentityFields.vue'");
    expect($detailsContents)->toContain('UserIdentityFields');
    expect($createPageContents)->toContain("from '@/components/admin/UserDetailsForm.vue'");
    expect($createPageContents)->toContain('UserDetailsForm');
    expect($settingsContents)->toContain("from '@/components/UserIdentityFields.vue'");
    expect($settingsContents)->toContain('UserIdentityFields');
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
        'resources/js/pages/settings/Appearance.vue',
        'resources/js/components/DeleteUser.vue',
    ];

    foreach ($settingsViews as $settingsView) {
        $contents = file_get_contents($projectRoot.'/'.$settingsView);

        expect($contents)->toContain("from '@/components/SettingsSectionCard.vue'");
        expect($contents)->toContain('SettingsSectionCard');
    }
});

it('reuses the shared settings action row across settings action surfaces', function () {
    $projectRoot = dirname(__DIR__, 2);
    $settingsViews = [
        'resources/js/pages/settings/Profile.vue',
        'resources/js/pages/settings/Password.vue',
        'resources/js/pages/settings/TwoFactor.vue',
        'resources/js/components/DeleteUser.vue',
    ];

    foreach ($settingsViews as $settingsView) {
        $contents = file_get_contents($projectRoot.'/'.$settingsView);

        expect($contents)->toContain("from '@/components/SettingsActionRow.vue'");
        expect($contents)->toContain('SettingsActionRow');
    }
});

it('keeps two-factor recovery codes free of nested card wrappers', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/TwoFactorRecoveryCodes.vue');

    expect($contents)->not->toContain("from '@/components/ui/card/Card.vue'");
    expect($contents)->not->toContain('CardHeader');
    expect($contents)->toContain('usePreferredReducedMotion');
    expect($contents)->toContain('grid-rows-[1fr]');
});

it('uses reduced-motion-safe motion patterns across shared shell surfaces', function () {
    $projectRoot = dirname(__DIR__, 2);

    $toastFeedContents = file_get_contents($projectRoot.'/resources/js/components/AppToasts.vue');
    $toastContents = file_get_contents($projectRoot.'/resources/js/components/toasts/AppToastItem.vue');
    $dialogContents = file_get_contents($projectRoot.'/resources/js/components/ui/dialog/variants.ts');
    $sheetContents = file_get_contents($projectRoot.'/resources/js/components/ui/sheet/variants.ts');
    $sidebarContents = file_get_contents($projectRoot.'/resources/js/components/ui/sidebar/variants.ts');

    expect($toastFeedContents)->not->toContain('transition: all');
    expect($toastContents)->toContain('transition-transform');
    expect($toastContents)->not->toContain('transition-[width]');
    expect($toastContents)->toContain('motion-reduce:transition-none');
    expect($dialogContents)->toContain('motion-reduce:data-[state=closed]:animate-none');
    expect($sheetContents)->toContain('motion-reduce:data-[state=open]:animate-none');
    expect($sidebarContents)->toContain('motion-reduce:transition-none');
    expect($sidebarContents)->not->toContain('transition-[width,height,padding]');
});

it('keeps key touch targets at mobile-friendly sizes across shared shell controls', function () {
    $headerCellContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/AdminIndexHeaderCell.vue');
    $headerUtilityContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/app-header/AppHeaderUtilityActions.vue');
    $mobileNavContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/app-header/AppHeaderMobileNavigation.vue');
    $navigationMenuContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/ui/navigation-menu/variants.ts');
    $inputOtpContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/ui/input-otp/variants.ts');
    $sidebarMenuActionContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/ui/sidebar/SidebarMenuAction.vue');
    $sidebarGroupActionContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/ui/sidebar/SidebarGroupAction.vue');
    $sidebarContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/ui/sidebar/variants.ts');

    expect($headerCellContents)->toContain('size="iconSm"');
    expect($headerUtilityContents)->toContain('h-11 w-11');
    expect($headerUtilityContents)->toContain('size-11');
    expect($mobileNavContents)->toContain('min-h-11');
    expect($mobileNavContents)->toContain('h-11 w-11');
    expect($navigationMenuContents)->toContain('h-11');
    expect($inputOtpContents)->toContain('h-11 w-11');
    expect($sidebarMenuActionContents)->toContain('size-8');
    expect($sidebarGroupActionContents)->toContain('size-8');
    expect($sidebarContents)->toContain('sidebarTriggerVariants = tv({ base: \'h-11 w-11\'');
});

it('renders breadcrumbs honestly when intermediate items do not have destinations', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/Breadcrumbs.vue');

    expect($contents)->toContain('v-else-if="item.href"');
    expect($contents)->not->toContain("item.href ?? '#'");
});

it('keeps dashboard quick links from collapsing into a blank area when no admin links are available', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/admin/AdminQuickLinks.vue');

    expect($contents)->toContain('id="admin-dashboard-quick-links-empty-state"');
    expect($contents)->toContain('No admin surfaces are assigned to this account yet.');
    expect($contents)->toContain('{{ item.command }}');
});

it('keeps admin index surfaces readable on narrow screens with dedicated mobile controls', function () {
    $projectRoot = dirname(__DIR__, 2);
    $usersContents = file_get_contents($projectRoot.'/resources/js/pages/admin/Users/Index.vue');
    $rolesContents = file_get_contents($projectRoot.'/resources/js/pages/admin/Roles/Index.vue');
    $permissionsContents = file_get_contents($projectRoot.'/resources/js/components/admin/PermissionIndexTable.vue');
    $tableCardContents = file_get_contents($projectRoot.'/resources/js/components/admin/AdminIndexTableCard.vue');

    expect($usersContents)->toContain('id="admin-users-index-mobile-controls"');
    expect($usersContents)->toContain('id="admin-users-index-mobile-list"');
    expect($usersContents)->toContain('as="toolbar"');

    expect($rolesContents)->toContain('id="admin-roles-index-mobile-controls"');
    expect($rolesContents)->toContain('id="admin-roles-index-mobile-list"');
    expect($rolesContents)->toContain('as="toolbar"');

    expect($permissionsContents)->toContain('Refine permissions');
    expect($permissionsContents)->toContain('as="toolbar"');
    expect($permissionsContents)->toContain("from '@/components/admin/AdminIndexTableCard.vue'");
    expect($tableCardContents)->toContain('overflow-hidden py-0 md:block');
});

it('keeps shared tokens and primitives free of legacy glass variants', function () {
    $projectRoot = dirname(__DIR__, 2);
    $cssContents = file_get_contents($projectRoot.'/resources/css/app.css');
    $cardContents = file_get_contents($projectRoot.'/resources/js/components/ui/card/variants.ts');
    $buttonContents = file_get_contents($projectRoot.'/resources/js/components/ui/button/variants.ts');
    $toastContents = file_get_contents($projectRoot.'/resources/js/composables/useToast.ts');

    expect($cssContents)->not->toContain('liquid-glass');
    expect($cardContents)->not->toContain("glass: '");
    expect($buttonContents)->not->toContain("| 'glass'");
    expect($toastContents)->not->toContain("'glass'");
});

it('keeps starter-kit copy and fallback branding out of the shared shell', function () {
    $projectRoot = dirname(__DIR__, 2);
    $welcomeContents = file_get_contents($projectRoot.'/resources/js/pages/Welcome.vue');
    $headerContents = file_get_contents($projectRoot.'/resources/js/components/AppHeader.vue');
    $sidebarContents = file_get_contents($projectRoot.'/resources/js/components/AppSidebar.vue');
    $appContents = file_get_contents($projectRoot.'/resources/js/app.ts');
    $ssrContents = file_get_contents($projectRoot.'/resources/js/ssr.ts');

    expect($welcomeContents)->not->toContain('laravel starter-kit');
    expect($headerContents)->not->toContain('docs');
    expect($sidebarContents)->not->toContain('docs');
    expect($appContents)->toContain("|| 'Southeast Code'");
    expect($ssrContents)->toContain("|| 'Southeast Code'");
});

it('keeps key icon-only controls explicitly named for assistive technology', function () {
    $mobileNavContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/app-header/AppHeaderMobileNavigation.vue');
    $headerUtilityContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/app-header/AppHeaderUtilityActions.vue');
    $twoFactorSetupStepContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/two-factor/TwoFactorSetupStep.vue');

    expect($mobileNavContents)->toContain('aria-label="Open navigation menu"');
    expect($headerUtilityContents)->toContain('aria-label="Open user menu"');
    expect($twoFactorSetupStepContents)->toContain("copied ? 'Manual setup key copied' : 'Copy manual setup key'");
});

it('keeps touched shell and auth utility surfaces on semantic token styling', function () {
    $projectRoot = dirname(__DIR__, 2);
    $textLinkContents = file_get_contents($projectRoot.'/resources/js/components/TextLink.vue');
    $appearanceTabsContents = file_get_contents($projectRoot.'/resources/js/components/AppearanceTabs.vue');
    $headerContents = file_get_contents($projectRoot.'/resources/js/components/AppHeader.vue');
    $toastContents = file_get_contents($projectRoot.'/resources/js/components/toasts/AppToastItem.vue');

    expect($textLinkContents)->toContain('decoration-border');
    expect($textLinkContents)->not->toContain('decoration-neutral');
    expect($appearanceTabsContents)->toContain('bg-muted/80');
    expect($appearanceTabsContents)->not->toContain('bg-neutral');
    expect($headerContents)->toContain('text-muted-foreground');
    expect($headerContents)->not->toContain('text-neutral');
    expect($toastContents)->toContain('bg-border/70');
});

it('uses VueUse primitives for the touched appearance and two-factor helper flows', function () {
    $appearanceContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/composables/useAppearance.ts');
    $twoFactorModalContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/TwoFactorSetupModal.vue');
    $recoveryCodesContents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/TwoFactorRecoveryCodes.vue');

    expect($appearanceContents)->toContain('useStorage');
    expect($appearanceContents)->toContain('usePreferredDark');
    expect($twoFactorModalContents)->toContain('useClipboard');
    expect($recoveryCodesContents)->toContain('usePreferredReducedMotion');
});

it('keeps auth state guidance calm, specific, and next-step oriented', function () {
    $projectRoot = dirname(__DIR__, 2);
    $confirmPasswordContents = file_get_contents($projectRoot.'/resources/js/pages/auth/ConfirmPassword.vue');
    $resetPasswordContents = file_get_contents($projectRoot.'/resources/js/pages/auth/ResetPassword.vue');
    $registerContents = file_get_contents($projectRoot.'/resources/js/pages/auth/Register.vue');
    $twoFactorChallengeContents = file_get_contents($projectRoot.'/resources/js/pages/auth/TwoFactorChallenge.vue');

    expect($confirmPasswordContents)->toContain('Confirm this action');
    expect($confirmPasswordContents)->toContain('protected step');

    expect($resetPasswordContents)->toContain('Choose the password you want to use next');
    expect($resetPasswordContents)->toContain('reset');
    expect($resetPasswordContents)->toContain('link will no longer be needed');

    expect($registerContents)->toContain('Set up account access');
    expect($registerContents)->toContain('After registration, continue with verification or sign in directly');

    expect($twoFactorChallengeContents)->toContain('This form accepts only the active code currently shown');
    expect($twoFactorChallengeContents)->toContain('your authenticator device is unavailable');
    expect($twoFactorChallengeContents)->toContain('<Spinner v-if="processing" />');
});

it('syncs input default values across inertial page navigations when not using v-model', function () {
    $contents = file_get_contents(dirname(__DIR__, 2).'/resources/js/components/ui/input/Input.vue');

    expect($contents)->toContain('watch(');
    expect($contents)->toContain('() => props.defaultValue');
    expect($contents)->toContain('if (props.modelValue !== undefined)');
    expect($contents)->toContain("modelValue.value = value ?? ''");
});
