<?php

declare(strict_types=1);

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use Modules\Audit\Actions\GetAuditHistoryItems;
use Modules\Audit\Actions\GetAuditLogFilterOptions;
use Modules\Audit\Actions\GetAuditLogIndexItems;
use Modules\Audit\Actions\IndexAuditLogs;
use Modules\Audit\Actions\RecordAuditLog;
use Modules\Audit\DTOs\AuditHistoryChangeData;
use Modules\Audit\DTOs\AuditHistoryItemData;
use Modules\Audit\DTOs\AuditLogIndexFilterOptionsData;
use Modules\Audit\DTOs\AuditLogIndexItemData;
use Modules\Audit\DTOs\AuditLogIndexQueryData;
use Modules\Audit\Http\Controllers\AuditLogsController;
use Modules\Audit\Http\Requests\IndexAuditLogsRequest;
use Modules\Audit\Listeners\RecordAuditableDomainEvent;
use Modules\Audit\Models\AuditLog;
use Modules\Auth\Actions\GetAuthCounts;
use Modules\Auth\Actions\RegisterUser;
use Modules\Auth\Actions\ResetUserPassword;
use Modules\Auth\DTOs\AuthCountsData;
use Modules\Core\Actions\AdminIndexQuery;
use Modules\Core\Actions\FormRequestRulesTransformer;
use Modules\Core\Actions\GetAdminIndex;
use Modules\Core\Actions\PasswordValidationRules;
use Modules\Core\Actions\UserIdentityValidationRules;
use Modules\Core\Contracts\AuditableDomainEvent;
use Modules\Core\DTOs\AdminIndexQueryData;
use Modules\Core\DTOs\AuthenticatedUserData;
use Modules\Core\DTOs\SharedAuthData;
use Modules\Core\Models\User;
use Modules\Dashboard\Actions\GetDashboardMetrics;
use Modules\Dashboard\Actions\GetDashboardSources;
use Modules\Dashboard\Contracts\DashboardMetricCounts;
use Modules\Dashboard\Contracts\DashboardMetricsProvider;
use Modules\Dashboard\DTOs\DashboardMetricSourceData;
use Modules\Dashboard\DTOs\DashboardOverviewSourceData;
use Modules\Dashboard\DTOs\DashboardSourcesData;
use Modules\Permissions\Actions\CountPermissions;
use Modules\Permissions\Actions\CreatePermission;
use Modules\Permissions\Actions\DeletePermission;
use Modules\Permissions\Actions\GetPermissionFilterOptions;
use Modules\Permissions\Actions\GetPermissionIndexItems;
use Modules\Permissions\Actions\IndexPermissions;
use Modules\Permissions\Actions\PermissionFilterOptionsCatalog;
use Modules\Permissions\Actions\PermissionGroupCatalog;
use Modules\Permissions\Actions\PermissionNormalizer;
use Modules\Permissions\Actions\UpdatePermission;
use Modules\Permissions\Console\CreatePermissionCommand;
use Modules\Permissions\Contracts\PermissionFilterOptionsProvider;
use Modules\Permissions\Contracts\PermissionGroupCatalogContract;
use Modules\Permissions\DTOs\CreatePermissionData;
use Modules\Permissions\DTOs\PermissionGroupOptionData;
use Modules\Permissions\DTOs\PermissionIndexFilterOptionsData;
use Modules\Permissions\DTOs\PermissionIndexItemData;
use Modules\Permissions\DTOs\PermissionItemData;
use Modules\Permissions\DTOs\UpdatePermissionData;
use Modules\Permissions\Events\PermissionCreated;
use Modules\Permissions\Events\PermissionDeleted;
use Modules\Permissions\Events\PermissionUpdated;
use Modules\Permissions\Exceptions\CannotRemoveRequiredSuperAdminPermissions;
use Modules\Permissions\Exceptions\UnknownPermissionsSelected;
use Modules\Permissions\Http\Controllers\PermissionsController;
use Modules\Permissions\Http\Requests\StorePermissionRequest;
use Modules\Permissions\Http\Requests\UpdatePermissionRequest;
use Modules\Permissions\Models\Permission;
use Modules\Permissions\Models\PermissionGroup;
use Modules\Roles\Actions\CountRoles;
use Modules\Roles\Actions\CreateRole;
use Modules\Roles\Actions\DeleteRole;
use Modules\Roles\Actions\GetEditableRoles;
use Modules\Roles\Actions\GetRoleFilterOptions;
use Modules\Roles\Actions\GetRoleIndexItems;
use Modules\Roles\Actions\GroupedPermissions;
use Modules\Roles\Actions\IndexRoles;
use Modules\Roles\Actions\RoleFilterOptionsCatalog;
use Modules\Roles\Actions\RoleNameNormalizer;
use Modules\Roles\Actions\SyncRolePermissions;
use Modules\Roles\Actions\UpdateRole;
use Modules\Roles\Console\CreateRoleCommand;
use Modules\Roles\Contracts\GroupedPermissionsProvider;
use Modules\Roles\Contracts\RoleFilterOptionsProvider;
use Modules\Roles\DTOs\CreateRoleData;
use Modules\Roles\DTOs\EditableRoleData;
use Modules\Roles\DTOs\RoleIndexFilterOptionsData;
use Modules\Roles\DTOs\RoleListItemData;
use Modules\Roles\DTOs\RoleOptionData;
use Modules\Roles\DTOs\SyncRolePermissionsData;
use Modules\Roles\DTOs\UpdateRoleData;
use Modules\Roles\Events\RoleCreated;
use Modules\Roles\Events\RoleDeleted;
use Modules\Roles\Events\RolePermissionsSynced;
use Modules\Roles\Events\RoleUpdated;
use Modules\Roles\Exceptions\CannotDeleteProtectedSuperAdminRole;
use Modules\Roles\Exceptions\CannotRemoveLastSuperAdminRoleAssignment;
use Modules\Roles\Exceptions\CannotRenameProtectedSuperAdminRole;
use Modules\Roles\Exceptions\UnknownRolesSelected;
use Modules\Roles\Http\Controllers\RolesController;
use Modules\Roles\Http\Requests\StoreRoleRequest;
use Modules\Roles\Http\Requests\SyncRolePermissionsRequest;
use Modules\Roles\Http\Requests\UpdateRoleRequest;
use Modules\Roles\Models\Role;
use Modules\Settings\Actions\DeleteProfile;
use Modules\Settings\Actions\UpdatePassword;
use Modules\Settings\Actions\UpdateProfile;
use Modules\Settings\DTOs\UpdateProfileData;
use Modules\Settings\Events\PasswordUpdated;
use Modules\Settings\Events\ProfileDeleted;
use Modules\Settings\Events\ProfileUpdated;
use Modules\Settings\Http\Controllers\PasswordController;
use Modules\Settings\Http\Controllers\ProfileController;
use Modules\Settings\Http\Controllers\TwoFactorAuthenticationController;
use Modules\Settings\Http\Requests\PasswordUpdateRequest;
use Modules\Settings\Http\Requests\ProfileDeleteRequest;
use Modules\Settings\Http\Requests\ProfileUpdateRequest;
use Modules\Settings\Http\Requests\TwoFactorAuthenticationRequest;
use Modules\Users\Actions\CountUsers;
use Modules\Users\Actions\CreateUser;
use Modules\Users\Actions\DeleteUser;
use Modules\Users\Actions\GetAssignableUsers;
use Modules\Users\Actions\GetUserFilterOptions;
use Modules\Users\Actions\GetUserIndexItems;
use Modules\Users\Actions\IndexUsers;
use Modules\Users\Actions\SyncUserRoles;
use Modules\Users\Actions\UpdateUser;
use Modules\Users\Actions\UserFilterOptionsCatalog;
use Modules\Users\Console\CreateUserCommand;
use Modules\Users\Contracts\UserFilterOptionsProvider;
use Modules\Users\DTOs\AssignableUserData;
use Modules\Users\DTOs\CreateUserData;
use Modules\Users\DTOs\EditableUserData;
use Modules\Users\DTOs\SyncUserRolesData;
use Modules\Users\DTOs\UpdateUserData;
use Modules\Users\DTOs\UserIndexFilterOptionsData;
use Modules\Users\DTOs\UserListItemData;
use Modules\Users\Events\UserCreated;
use Modules\Users\Events\UserDeleted;
use Modules\Users\Events\UserRolesSynced;
use Modules\Users\Events\UserUpdated;
use Modules\Users\Exceptions\CannotDeleteLastSuperAdminUser;
use Modules\Users\Http\Controllers\UsersController;
use Modules\Users\Http\Requests\StoreUserRequest;
use Modules\Users\Http\Requests\SyncUserRolesRequest;
use Modules\Users\Http\Requests\UpdateUserRequest;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

dataset('project_write_action_classes', [
    CreateUser::class,
    UpdateUser::class,
    DeleteUser::class,
    SyncUserRoles::class,
    CreateRole::class,
    UpdateRole::class,
    DeleteRole::class,
    SyncRolePermissions::class,
    CreatePermission::class,
    UpdatePermission::class,
    DeletePermission::class,
    UpdateProfile::class,
    DeleteProfile::class,
    UpdatePassword::class,
]);

dataset('project_read_action_classes', [
    GetDashboardMetrics::class,
    GetDashboardSources::class,
    CountUsers::class,
    CountRoles::class,
    CountPermissions::class,
    IndexAuditLogs::class,
    GetAuditHistoryItems::class,
    GetAuditLogIndexItems::class,
    GetAuditLogFilterOptions::class,
    GetAdminIndex::class,
    IndexUsers::class,
    GetUserIndexItems::class,
    GetUserFilterOptions::class,
    GetEditableRoles::class,
    IndexRoles::class,
    GetRoleIndexItems::class,
    GetRoleFilterOptions::class,
    GetAssignableUsers::class,
    GetPermissionFilterOptions::class,
    GetPermissionIndexItems::class,
    IndexPermissions::class,
]);

dataset('admin_guarded_form_requests', [
    [StoreUserRequest::class, "can('users.create')"],
    [UpdateUserRequest::class, "can('users.update')"],
    [SyncUserRolesRequest::class, "can('users.assignRoles')"],
    [StoreRoleRequest::class, "can('roles.create')"],
    [UpdateRoleRequest::class, "can('roles.update')"],
    [SyncRolePermissionsRequest::class, "can('roles.assignPermissions')"],
    [StorePermissionRequest::class, "can('permissions.create')"],
    [UpdatePermissionRequest::class, "can('permissions.update')"],
]);

dataset('settings_guarded_form_requests', [
    [PasswordUpdateRequest::class, 'return $this->user() !== null;'],
    [ProfileDeleteRequest::class, 'return $this->user() !== null;'],
    [ProfileUpdateRequest::class, 'return $this->user() !== null;'],
    [TwoFactorAuthenticationRequest::class, 'Features::enabled(Features::twoFactorAuthentication()) && $this->user() !== null'],
]);

dataset('fortify_action_adapters', [
    [RegisterUser::class, CreatesNewUsers::class, 'create'],
    [ResetUserPassword::class, ResetsUserPasswords::class, 'reset'],
]);

dataset('auditable_event_classes', [
    UserCreated::class,
    UserUpdated::class,
    UserDeleted::class,
    UserRolesSynced::class,
    RoleCreated::class,
    RoleUpdated::class,
    RoleDeleted::class,
    RolePermissionsSynced::class,
    PermissionCreated::class,
    PermissionUpdated::class,
    PermissionDeleted::class,
    ProfileUpdated::class,
    PasswordUpdated::class,
    ProfileDeleted::class,
]);

dataset('backend_data_classes', [
    AdminIndexQueryData::class,
    DashboardMetricSourceData::class,
    DashboardOverviewSourceData::class,
    DashboardSourcesData::class,
    AuthCountsData::class,
    AuditHistoryChangeData::class,
    AuditHistoryItemData::class,
    AuditLogIndexFilterOptionsData::class,
    AuditLogIndexItemData::class,
    AuditLogIndexQueryData::class,
    CreateUserData::class,
    UpdateUserData::class,
    EditableUserData::class,
    RoleOptionData::class,
    SyncUserRolesData::class,
    UserIndexFilterOptionsData::class,
    UserListItemData::class,
    CreateRoleData::class,
    UpdateRoleData::class,
    EditableRoleData::class,
    AssignableUserData::class,
    RoleIndexFilterOptionsData::class,
    RoleListItemData::class,
    SyncRolePermissionsData::class,
    CreatePermissionData::class,
    UpdatePermissionData::class,
    PermissionItemData::class,
    PermissionIndexFilterOptionsData::class,
    PermissionIndexItemData::class,
    PermissionGroupOptionData::class,
    AuthenticatedUserData::class,
    SharedAuthData::class,
    UpdateProfileData::class,
]);

dataset('typescript_contract_classes', [
    IndexAuditLogsRequest::class,
    AuditHistoryChangeData::class,
    AuditHistoryItemData::class,
    AuditLogIndexFilterOptionsData::class,
    AuditLogIndexItemData::class,
    AuditLogIndexQueryData::class,
    StoreUserRequest::class,
    UpdateUserRequest::class,
    SyncUserRolesRequest::class,
    StoreRoleRequest::class,
    UpdateRoleRequest::class,
    SyncRolePermissionsRequest::class,
    StorePermissionRequest::class,
    UpdatePermissionRequest::class,
    ProfileUpdateRequest::class,
    ProfileDeleteRequest::class,
    PasswordUpdateRequest::class,
    TwoFactorAuthenticationRequest::class,
    AuthenticatedUserData::class,
    SharedAuthData::class,
    AdminIndexQueryData::class,
    DashboardMetricSourceData::class,
    DashboardOverviewSourceData::class,
    DashboardSourcesData::class,
    EditableUserData::class,
    RoleOptionData::class,
    UserIndexFilterOptionsData::class,
    UserListItemData::class,
    AssignableUserData::class,
    EditableRoleData::class,
    RoleIndexFilterOptionsData::class,
    RoleListItemData::class,
    PermissionGroupOptionData::class,
    PermissionIndexFilterOptionsData::class,
    PermissionIndexItemData::class,
    PermissionItemData::class,
]);

dataset('module_collaborator_classes', [
    [AdminIndexQuery::class, 'Modules\\Core\\Actions'],
    [FormRequestRulesTransformer::class, 'Modules\\Core\\Actions'],
    [PasswordValidationRules::class, 'Modules\\Core\\Actions'],
    [GetAuthCounts::class, 'Modules\\Auth\\Actions'],
    [PermissionFilterOptionsCatalog::class, 'Modules\\Permissions\\Actions'],
    [PermissionGroupCatalog::class, 'Modules\\Permissions\\Actions'],
    [PermissionNormalizer::class, 'Modules\\Permissions\\Actions'],
    [GroupedPermissions::class, 'Modules\\Roles\\Actions'],
    [RoleFilterOptionsCatalog::class, 'Modules\\Roles\\Actions'],
    [RoleNameNormalizer::class, 'Modules\\Roles\\Actions'],
    [UserFilterOptionsCatalog::class, 'Modules\\Users\\Actions'],
    [UserIdentityValidationRules::class, 'Modules\\Core\\Actions'],
    [CannotRenameProtectedSuperAdminRole::class, 'Modules\\Roles\\Exceptions'],
    [CannotDeleteProtectedSuperAdminRole::class, 'Modules\\Roles\\Exceptions'],
    [CannotRemoveRequiredSuperAdminPermissions::class, 'Modules\\Permissions\\Exceptions'],
    [CannotDeleteLastSuperAdminUser::class, 'Modules\\Users\\Exceptions'],
    [CannotRemoveLastSuperAdminRoleAssignment::class, 'Modules\\Roles\\Exceptions'],
    [UnknownPermissionsSelected::class, 'Modules\\Permissions\\Exceptions'],
    [UnknownRolesSelected::class, 'Modules\\Roles\\Exceptions'],
]);

dataset('module_contract_classes', [
    [DashboardMetricCounts::class, 'Modules\\Dashboard\\Contracts'],
    [DashboardMetricsProvider::class, 'Modules\\Dashboard\\Contracts'],
    [PermissionGroupCatalogContract::class, 'Modules\\Permissions\\Contracts'],
    [PermissionFilterOptionsProvider::class, 'Modules\\Permissions\\Contracts'],
    [GroupedPermissionsProvider::class, 'Modules\\Roles\\Contracts'],
    [RoleFilterOptionsProvider::class, 'Modules\\Roles\\Contracts'],
    [UserFilterOptionsProvider::class, 'Modules\\Users\\Contracts'],
]);

dataset('slice_transport_classes', [
    [AuditLogsController::class, 'Modules\\Audit\\Http\\Controllers'],
    [UsersController::class, 'Modules\\Users\\Http\\Controllers'],
    [RolesController::class, 'Modules\\Roles\\Http\\Controllers'],
    [PermissionsController::class, 'Modules\\Permissions\\Http\\Controllers'],
    [ProfileController::class, 'Modules\\Settings\\Http\\Controllers'],
    [PasswordController::class, 'Modules\\Settings\\Http\\Controllers'],
    [TwoFactorAuthenticationController::class, 'Modules\\Settings\\Http\\Controllers'],
]);

dataset('module_request_classes', [
    [IndexAuditLogsRequest::class, 'Modules\\Audit\\Http\\Requests'],
    [StoreUserRequest::class, 'Modules\\Users\\Http\\Requests'],
    [UpdateUserRequest::class, 'Modules\\Users\\Http\\Requests'],
    [SyncUserRolesRequest::class, 'Modules\\Users\\Http\\Requests'],
    [StoreRoleRequest::class, 'Modules\\Roles\\Http\\Requests'],
    [UpdateRoleRequest::class, 'Modules\\Roles\\Http\\Requests'],
    [SyncRolePermissionsRequest::class, 'Modules\\Roles\\Http\\Requests'],
    [StorePermissionRequest::class, 'Modules\\Permissions\\Http\\Requests'],
    [UpdatePermissionRequest::class, 'Modules\\Permissions\\Http\\Requests'],
    [ProfileUpdateRequest::class, 'Modules\\Settings\\Http\\Requests'],
    [ProfileDeleteRequest::class, 'Modules\\Settings\\Http\\Requests'],
    [PasswordUpdateRequest::class, 'Modules\\Settings\\Http\\Requests'],
    [TwoFactorAuthenticationRequest::class, 'Modules\\Settings\\Http\\Requests'],
]);

dataset('eloquent_metadata_models', [
    [
        User::class,
        ['#[Table(name: \'users\')]', '#[UseFactory(UserFactory::class)]', '#[Fillable([', '#[Hidden(['],
        ['protected $fillable', 'protected $hidden', 'protected static function newFactory'],
    ],
    [
        AuditLog::class,
        ['#[Table(name: \'audit_logs\', timestamps: false)]', '#[Fillable(['],
        ['#[WithoutTimestamps]', 'public $timestamps', 'protected $fillable'],
    ],
    [
        Permission::class,
        ['#[Table(name: \'permissions\')]', '#[Fillable(['],
        ['protected $fillable'],
    ],
    [
        PermissionGroup::class,
        ['#[Table(name: \'permission_groups\')]', '#[Fillable(['],
        ['protected $fillable'],
    ],
    [
        Role::class,
        ['#[Table(name: \'roles\')]', '#[Fillable(['],
        ['protected $fillable'],
    ],
]);

dataset('artisan_attribute_commands', [
    [
        CreateUserCommand::class,
        ['#[Signature(\'create:user\')]', '#[Description(\'Interactively create a user via the CreateUser action.\')]'],
        ['protected $signature', 'protected $description'],
    ],
    [
        CreateRoleCommand::class,
        ['#[Signature(\'create:role\')]', '#[Description(\'Interactively create a role via the CreateRole action.\')]'],
        ['protected $signature', 'protected $description'],
    ],
    [
        CreatePermissionCommand::class,
        ['#[Signature(\'create:permission\')]', '#[Description(\'Interactively create a permission via the CreatePermission action.\')]'],
        ['protected $signature', 'protected $description'],
    ],
]);

dataset('controller_attribute_metadata', [
    [AuditLogsController::class, ['#[Authorize(\'audit_logs.view\')]']],
    [
        UsersController::class,
        [
            '#[Authorize(\'users.view\')]',
            '#[Authorize(\'users.create\')]',
            '#[Authorize(\'users.update\')]',
            '#[Authorize(\'users.delete\')]',
            '#[Authorize(\'users.assignRoles\')]',
        ],
    ],
    [
        RolesController::class,
        [
            '#[Authorize(\'roles.view\')]',
            '#[Authorize(\'roles.create\')]',
            '#[Authorize(\'roles.update\')]',
            '#[Authorize(\'roles.delete\')]',
            '#[Authorize(\'roles.assignPermissions\')]',
        ],
    ],
    [
        PermissionsController::class,
        [
            '#[Authorize(\'permissions.view\')]',
            '#[Authorize(\'permissions.create\')]',
            '#[Authorize(\'permissions.update\')]',
            '#[Authorize(\'permissions.delete\')]',
        ],
    ],
    [PasswordController::class, ['#[Middleware(\'throttle:6,1\')]']],
]);

it('keeps internal write orchestration on action classes with a public static handle entrypoint', function (string $actionClass): void {
    $reflection = new ReflectionClass($actionClass);

    expect($reflection->getNamespaceName())->toStartWith('Modules\\');
    expect($reflection->getNamespaceName())->toContain('\\Actions');
    expect($reflection->hasMethod('__invoke'))->toBeFalse();
    expect(backendArchitectureDeclaredPublicMethodNames($reflection))->toContain('handle');

    assertStaticTypedPublicMethods($reflection);
})->with('project_write_action_classes');

it('keeps internal write actions small enough for single-purpose handle methods', function (string $actionClass): void {
    $reflection = new ReflectionClass($actionClass);
    $handleMethod = $reflection->getMethod('handle');

    expect(backendArchitectureMethodLineCount($handleMethod))->toBeLessThanOrEqual(30);
})->with('project_write_action_classes');

it('keeps write actions transactional and dispatches auditable domain events instead of touching audit internals', function (string $actionClass): void {
    $reflection = new ReflectionClass($actionClass);
    $contents = file_get_contents($reflection->getFileName());

    expect($contents)->toContain('DB::transaction');
    expect($contents)->toContain('event(new ');
    expect($contents)->not->toContain('RecordAuditLog::handle');
    expect($contents)->not->toContain('ScheduleAuditLog::handle');
})->with('project_write_action_classes');

it('keeps read-side collaborators on action classes with a public static handle entrypoint', function (string $actionClass): void {
    $reflection = new ReflectionClass($actionClass);

    expect($reflection->getNamespaceName())->toStartWith('Modules\\');
    expect($reflection->getNamespaceName())->toContain('\\Actions');
    expect($reflection->hasMethod('__invoke'))->toBeFalse();
    expect(backendArchitectureDeclaredPublicMethodNames($reflection))->toContain('handle');

    assertStaticTypedPublicMethods($reflection);
})->with('project_read_action_classes');

it('keeps read-side action handlers small enough for focused orchestration', function (string $actionClass): void {
    $reflection = new ReflectionClass($actionClass);
    $handleMethod = $reflection->getMethod('handle');

    expect(backendArchitectureMethodLineCount($handleMethod))->toBeLessThanOrEqual(30);
})->with('project_read_action_classes');

it('keeps Fortify adapter actions on their vendor contract methods instead of the project handle api', function (
    string $actionClass,
    string $contract,
    string $contractMethod,
): void {
    $reflection = new ReflectionClass($actionClass);

    expect($reflection->getNamespaceName())->toBe('Modules\\Auth\\Actions');
    expect($reflection->isFinal())->toBeTrue();
    expect($reflection->implementsInterface($contract))->toBeTrue();
    expect($reflection->hasMethod($contractMethod))->toBeTrue();
    expect($reflection->hasMethod('handle'))->toBeFalse();
    expect(array_diff(
        backendArchitectureDeclaredPublicMethodNames($reflection),
        ['__construct', $contractMethod],
    ))->toBe([]);
})->with('fortify_action_adapters');

it('uses spatie data objects for non-trivial admin and shared payload contracts', function (string $dataClass): void {
    $reflection = new ReflectionClass($dataClass);

    expect($reflection->getNamespaceName())->toStartWith('Modules\\');
    expect($reflection->getNamespaceName())->toContain('\\DTOs');
    expect($reflection->isFinal())->toBeTrue();
    expect($reflection->isSubclassOf(Data::class))->toBeTrue();
})->with('backend_data_classes');

it('keeps module collaborators in approved module namespaces', function (
    string $className,
    string $expectedNamespace,
): void {
    $reflection = new ReflectionClass($className);

    expect($reflection->getNamespaceName())->toBe($expectedNamespace);
})->with('module_collaborator_classes');

it('keeps feature modules in the package-native Modules root', function (): void {
    $projectRoot = dirname(__DIR__, 2);
    $moduleDirectories = glob($projectRoot.'/Modules/*', GLOB_ONLYDIR) ?: [];

    expect($moduleDirectories)->not->toBeEmpty();
    expect($projectRoot.'/app/Modules')->not->toBeDirectory();

    foreach (['Audit', 'Auth', 'Core', 'Dashboard', 'Marketing', 'Permissions', 'Roles', 'Settings', 'Users'] as $moduleName) {
        expect($projectRoot."/Modules/{$moduleName}/app")->toBeDirectory();
        expect($projectRoot."/Modules/{$moduleName}/routes/web.php")->toBeFile();
        expect($projectRoot."/Modules/{$moduleName}/composer.json")->toBeFile();
    }
});

it('keeps legacy php classes out of app support and concerns roots', function (): void {
    $projectRoot = dirname(__DIR__, 2);
    $legacyRoots = [
        $projectRoot.'/app/Support',
        $projectRoot.'/app/Concerns',
    ];

    foreach ($legacyRoots as $legacyRoot) {
        if (! is_dir($legacyRoot)) {
            expect($legacyRoot)->not->toBeDirectory();

            continue;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($legacyRoot));

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile()) {
                continue;
            }

            expect($fileInfo->getExtension())->not->toBe('php');
        }
    }
});

it('keeps reusable admin collaborators behind narrow module-owned contracts', function (
    string $className,
    string $expectedNamespace,
): void {
    $reflection = new ReflectionClass($className);

    expect($reflection->isInterface())->toBeTrue();
    expect($reflection->getNamespaceName())->toBe($expectedNamespace);
})->with('module_contract_classes');

it('keeps admin and settings transport classes in slice-oriented http namespaces', function (
    string $className,
    string $expectedNamespace,
): void {
    $reflection = new ReflectionClass($className);

    expect($reflection->getNamespaceName())->toBe($expectedNamespace);
})->with('slice_transport_classes');

it('keeps module-owned form requests inside their module namespaces', function (
    string $className,
    string $expectedNamespace,
): void {
    $reflection = new ReflectionClass($className);

    expect($reflection->getNamespaceName())->toBe($expectedNamespace);
})->with('module_request_classes');

it('keeps concrete eloquent models on native laravel metadata attributes', function (
    string $className,
    array $requiredSnippets,
    array $legacySnippets,
): void {
    $reflection = new ReflectionClass($className);
    $contents = file_get_contents($reflection->getFileName());

    foreach ($requiredSnippets as $requiredSnippet) {
        expect($contents)->toContain($requiredSnippet);
    }

    foreach ($legacySnippets as $legacySnippet) {
        expect($contents)->not->toContain($legacySnippet);
    }
})->with('eloquent_metadata_models');

it('keeps artisan command metadata on native laravel command attributes', function (
    string $className,
    array $requiredSnippets,
    array $legacySnippets,
): void {
    $reflection = new ReflectionClass($className);
    $contents = file_get_contents($reflection->getFileName());

    foreach ($requiredSnippets as $requiredSnippet) {
        expect($contents)->toContain($requiredSnippet);
    }

    foreach ($legacySnippets as $legacySnippet) {
        expect($contents)->not->toContain($legacySnippet);
    }
})->with('artisan_attribute_commands');

it('keeps controller authorization and route-specific middleware on controller attributes', function (
    string $className,
    array $requiredSnippets,
): void {
    $reflection = new ReflectionClass($className);
    $contents = file_get_contents($reflection->getFileName());

    foreach ($requiredSnippets as $requiredSnippet) {
        expect($contents)->toContain($requiredSnippet);
    }
})->with('controller_attribute_metadata');

it('keeps route-specific authorization and throttling middleware out of the route files', function (): void {
    $permissionsRoutes = file_get_contents(dirname(__DIR__, 2).'/Modules/Permissions/routes/web.php');
    $settingsRoutes = file_get_contents(dirname(__DIR__, 2).'/Modules/Settings/routes/web.php');

    expect($permissionsRoutes)->not->toContain("->middleware('can:");
    expect($settingsRoutes)->not->toContain("->middleware('throttle:6,1')");
});

it('keeps the audit logger on a flat module action with a static handle entrypoint', function (): void {
    $reflection = new ReflectionClass(RecordAuditLog::class);

    expect($reflection->getNamespaceName())->toBe('Modules\\Audit\\Actions');
    expect($reflection->isFinal())->toBeTrue();
    expect(backendArchitectureDeclaredPublicMethodNames($reflection))->toContain('handle');

    assertStaticTypedPublicMethods($reflection);
});

it('keeps auditable domain events after-commit and behind shared contracts', function (string $eventClass): void {
    $reflection = new ReflectionClass($eventClass);

    expect($reflection->getNamespaceName())->toStartWith('Modules\\');
    expect($reflection->getNamespaceName())->toContain('\\Events');
    expect($reflection->isFinal())->toBeTrue();
    expect($reflection->implementsInterface(AuditableDomainEvent::class))->toBeTrue();
    expect($reflection->implementsInterface(ShouldDispatchAfterCommit::class))->toBeTrue();
})->with('auditable_event_classes');

it('keeps audit recording behind an interface-driven listener', function (): void {
    $reflection = new ReflectionClass(RecordAuditableDomainEvent::class);
    $contents = file_get_contents($reflection->getFileName());

    expect($reflection->getNamespaceName())->toBe('Modules\\Audit\\Listeners');
    expect($reflection->isFinal())->toBeTrue();
    expect($contents)->toContain('AuditableDomainEvent $event');
    expect($contents)->toContain('RecordAuditLog::handle');
});

it('uses package-native module providers instead of custom bootstrap discovery globs', function (): void {
    $contents = file_get_contents(dirname(__DIR__, 2).'/bootstrap/app.php');

    expect($contents)->not->toContain('->withEvents([');
    expect($contents)->not->toContain('->withCommands([');
    expect($contents)->not->toContain("app_path('Modules");
});

it('keeps audit writes centralized behind domain events instead of direct recorders', function (): void {
    $projectRoot = dirname(__DIR__, 2);
    $forbiddenSnippets = [
        'RecordAuditLog::handle',
        'ScheduleAuditLog::handle',
    ];
    $moduleDirectories = glob($projectRoot.'/Modules/*', GLOB_ONLYDIR) ?: [];

    foreach ($moduleDirectories as $moduleDirectory) {
        if (basename($moduleDirectory) === 'Audit') {
            continue;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($moduleDirectory.'/app'));

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($fileInfo->getPathname());

            foreach ($forbiddenSnippets as $forbiddenSnippet) {
                expect($contents)->not->toContain($forbiddenSnippet);
            }
        }
    }
});

it('uses package-native module namespaces rather than legacy app module namespaces', function (): void {
    $projectRoot = dirname(__DIR__, 2);
    $moduleDirectories = glob($projectRoot.'/Modules/*', GLOB_ONLYDIR) ?: [];

    foreach ($moduleDirectories as $moduleDirectory) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($moduleDirectory.'/app'));

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($fileInfo->getPathname());

            expect($contents)->not->toContain('App\\Modules\\');
        }
    }
});

it('keeps privileged admin form requests behind explicit permission checks', function (
    string $className,
    string $expectedAbilitySnippet,
): void {
    $reflection = new ReflectionClass($className);
    $contents = file_get_contents($reflection->getFileName());

    expect($contents)->toContain($expectedAbilitySnippet);
    expect($contents)->not->toContain('return true;');
})->with('admin_guarded_form_requests');

it('keeps settings form requests behind explicit authenticated-user or feature guards', function (
    string $className,
    string $expectedGuardSnippet,
): void {
    $reflection = new ReflectionClass($className);
    $contents = file_get_contents($reflection->getFileName());

    expect($contents)->toContain($expectedGuardSnippet);
})->with('settings_guarded_form_requests');

it('marks frontend-bound form requests and dto payloads for TypeScript generation', function (string $className): void {
    $reflection = new ReflectionClass($className);

    expect($reflection->getAttributes(TypeScript::class))->toHaveCount(1);
})->with('typescript_contract_classes');

it('reuses the shared user identity validation collaborator across settings, auth, console, and admin user flows', function (): void {
    $projectRoot = dirname(__DIR__, 2);

    expect($projectRoot.'/Modules/Auth/app/Actions/ProfileValidationRules.php')->not->toBeFile();
    expect($projectRoot.'/Modules/Core/app/Actions/UserIdentityValidationRules.php')->toBeFile();

    foreach ([
        '/Modules/Users/app/Console/CreateUserCommand.php',
        '/Modules/Auth/app/Actions/ValidateRegistrationInput.php',
        '/Modules/Users/app/Http/Requests/StoreUserRequest.php',
        '/Modules/Users/app/Http/Requests/UpdateUserRequest.php',
        '/Modules/Settings/app/Http/Requests/ProfileUpdateRequest.php',
    ] as $path) {
        $contents = file_get_contents($projectRoot.$path);

        expect($contents)->toContain('UserIdentityValidationRules');
        expect($contents)->not->toContain('ProfileValidationRules');
    }
});

it('keeps interactive artisan commands inside package-native modules', function (): void {
    $projectRoot = dirname(__DIR__, 2);

    foreach ([
        '/Modules/Core/app/Console/BaseInteractiveCreateCommand.php',
        '/Modules/Permissions/app/Console/CreatePermissionCommand.php',
        '/Modules/Roles/app/Console/CreateRoleCommand.php',
        '/Modules/Users/app/Console/CreateUserCommand.php',
    ] as $path) {
        expect($projectRoot.$path)->toBeFile();
    }

    foreach ([
        '/app/Console/Commands/BaseInteractiveCreateCommand.php',
        '/app/Console/Commands/CreatePermissionCommand.php',
        '/app/Console/Commands/CreateRoleCommand.php',
        '/app/Console/Commands/CreateUserCommand.php',
        '/app/Modules/IAM/Commands/BaseInteractiveCreateCommand.php',
        '/app/Modules/IAM/Permissions/Commands/CreatePermissionCommand.php',
        '/app/Modules/IAM/Roles/Commands/CreateRoleCommand.php',
        '/app/Modules/IAM/Users/Commands/CreateUserCommand.php',
    ] as $path) {
        expect($projectRoot.$path)->not->toBeFile();
    }

    $bootstrapContents = file_get_contents($projectRoot.'/bootstrap/app.php');

    expect($bootstrapContents)->not->toContain('->withCommands([');
});

it('keeps frontend page prop contracts split by domain instead of a monolithic page props file', function (): void {
    $projectRoot = dirname(__DIR__, 2);
    $splitTypeFiles = [
        $projectRoot.'/resources/js/types/admin/dashboard.ts',
        $projectRoot.'/resources/js/types/admin/permissions.ts',
        $projectRoot.'/resources/js/types/admin/roles.ts',
        $projectRoot.'/resources/js/types/admin/shared.ts',
        $projectRoot.'/resources/js/types/admin/users.ts',
        $projectRoot.'/resources/js/types/settings.ts',
    ];

    expect($projectRoot.'/resources/js/types/page-props.ts')->not->toBeFile();

    foreach ($splitTypeFiles as $splitTypeFile) {
        expect($splitTypeFile)->toBeFile();
    }
});

/** @return list<string> */
function backendArchitectureDeclaredPublicMethodNames(ReflectionClass $reflection): array
{
    $methods = array_values(array_map(
        fn (ReflectionMethod $method): string => $method->getName(),
        array_filter(
            $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
            fn (ReflectionMethod $method): bool => $method->getDeclaringClass()->getName() === $reflection->getName(),
        ),
    ));

    sort($methods);

    return $methods;
}

function backendArchitectureMethodLineCount(ReflectionMethod $method): int
{
    return $method->getEndLine() - $method->getStartLine() + 1;
}

function assertStaticTypedPublicMethods(ReflectionClass $reflection): void
{
    foreach (backendArchitectureDeclaredPublicMethods($reflection) as $method) {
        expect($method->isStatic())->toBeTrue();
        expect($method->hasReturnType())->toBeTrue();

        foreach ($method->getParameters() as $parameter) {
            expect($parameter->hasType())->toBeTrue();
        }
    }
}

/** @return list<ReflectionMethod> */
function backendArchitectureDeclaredPublicMethods(ReflectionClass $reflection): array
{
    return array_values(array_filter(
        $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
        fn (ReflectionMethod $method): bool => $method->getDeclaringClass()->getName() === $reflection->getName(),
    ));
}
