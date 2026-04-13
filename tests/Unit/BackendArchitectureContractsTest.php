<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuditLogsController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use App\Modules\Audit\Actions\GetAuditHistoryItems;
use App\Modules\Audit\Actions\GetAuditLogFilterOptions;
use App\Modules\Audit\Actions\GetAuditLogIndexItems;
use App\Modules\Audit\Actions\IndexAuditLogs;
use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditHistoryChangeData;
use App\Modules\Audit\DTOs\AuditHistoryItemData;
use App\Modules\Audit\DTOs\AuditLogIndexFilterOptionsData;
use App\Modules\Audit\DTOs\AuditLogIndexItemData;
use App\Modules\Audit\DTOs\AuditLogIndexQueryData;
use App\Modules\Audit\Listeners\RecordAuditableDomainEvent;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Audit\Requests\IndexAuditLogsRequest;
use App\Modules\Dashboard\Actions\GetDashboardMetrics;
use App\Modules\Dashboard\Actions\GetDashboardSources;
use App\Modules\Dashboard\Contracts\DashboardMetricCounts;
use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;
use App\Modules\Dashboard\DTOs\DashboardMetricSourceData;
use App\Modules\Dashboard\DTOs\DashboardOverviewSourceData;
use App\Modules\Dashboard\DTOs\DashboardSourcesData;
use App\Modules\IAM\Auth\Actions\GetAuthCounts;
use App\Modules\IAM\Auth\Actions\RegisterUser;
use App\Modules\IAM\Auth\Actions\ResetUserPassword;
use App\Modules\IAM\Auth\DTOs\AuthCountsData;
use App\Modules\IAM\DTOs\AuthenticatedUserData;
use App\Modules\IAM\DTOs\SharedAuthData;
use App\Modules\IAM\Permissions\Actions\CountPermissions;
use App\Modules\IAM\Permissions\Actions\CreatePermission;
use App\Modules\IAM\Permissions\Actions\DeletePermission;
use App\Modules\IAM\Permissions\Actions\GetPermissionFilterOptions;
use App\Modules\IAM\Permissions\Actions\GetPermissionIndexItems;
use App\Modules\IAM\Permissions\Actions\IndexPermissions;
use App\Modules\IAM\Permissions\Actions\PermissionFilterOptionsCatalog;
use App\Modules\IAM\Permissions\Actions\PermissionGroupCatalog;
use App\Modules\IAM\Permissions\Actions\PermissionNormalizer;
use App\Modules\IAM\Permissions\Actions\UpdatePermission;
use App\Modules\IAM\Permissions\Contracts\PermissionFilterOptionsProvider;
use App\Modules\IAM\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\IAM\Permissions\DTOs\CreatePermissionData;
use App\Modules\IAM\Permissions\DTOs\PermissionGroupOptionData;
use App\Modules\IAM\Permissions\DTOs\PermissionIndexFilterOptionsData;
use App\Modules\IAM\Permissions\DTOs\PermissionIndexItemData;
use App\Modules\IAM\Permissions\DTOs\PermissionItemData;
use App\Modules\IAM\Permissions\DTOs\UpdatePermissionData;
use App\Modules\IAM\Permissions\Events\PermissionCreated;
use App\Modules\IAM\Permissions\Events\PermissionDeleted;
use App\Modules\IAM\Permissions\Events\PermissionUpdated;
use App\Modules\IAM\Permissions\Exceptions\CannotRemoveRequiredSuperAdminPermissions;
use App\Modules\IAM\Permissions\Exceptions\UnknownPermissionsSelected;
use App\Modules\IAM\Permissions\Models\Permission;
use App\Modules\IAM\Permissions\Models\PermissionGroup;
use App\Modules\IAM\Permissions\Requests\StorePermissionRequest;
use App\Modules\IAM\Permissions\Requests\UpdatePermissionRequest;
use App\Modules\IAM\Roles\Actions\CountRoles;
use App\Modules\IAM\Roles\Actions\CreateRole;
use App\Modules\IAM\Roles\Actions\DeleteRole;
use App\Modules\IAM\Roles\Actions\GetEditableRoles;
use App\Modules\IAM\Roles\Actions\GetRoleFilterOptions;
use App\Modules\IAM\Roles\Actions\GetRoleIndexItems;
use App\Modules\IAM\Roles\Actions\GroupedPermissions;
use App\Modules\IAM\Roles\Actions\IndexRoles;
use App\Modules\IAM\Roles\Actions\RoleFilterOptionsCatalog;
use App\Modules\IAM\Roles\Actions\RoleNameNormalizer;
use App\Modules\IAM\Roles\Actions\SyncRolePermissions;
use App\Modules\IAM\Roles\Actions\UpdateRole;
use App\Modules\IAM\Roles\Contracts\GroupedPermissionsProvider;
use App\Modules\IAM\Roles\Contracts\RoleFilterOptionsProvider;
use App\Modules\IAM\Roles\DTOs\CreateRoleData;
use App\Modules\IAM\Roles\DTOs\EditableRoleData;
use App\Modules\IAM\Roles\DTOs\RoleIndexFilterOptionsData;
use App\Modules\IAM\Roles\DTOs\RoleListItemData;
use App\Modules\IAM\Roles\DTOs\RoleOptionData;
use App\Modules\IAM\Roles\DTOs\SyncRolePermissionsData;
use App\Modules\IAM\Roles\DTOs\UpdateRoleData;
use App\Modules\IAM\Roles\Events\RoleCreated;
use App\Modules\IAM\Roles\Events\RoleDeleted;
use App\Modules\IAM\Roles\Events\RolePermissionsSynced;
use App\Modules\IAM\Roles\Events\RoleUpdated;
use App\Modules\IAM\Roles\Exceptions\CannotDeleteProtectedSuperAdminRole;
use App\Modules\IAM\Roles\Exceptions\CannotRemoveLastSuperAdminRoleAssignment;
use App\Modules\IAM\Roles\Exceptions\CannotRenameProtectedSuperAdminRole;
use App\Modules\IAM\Roles\Exceptions\UnknownRolesSelected;
use App\Modules\IAM\Roles\Requests\StoreRoleRequest;
use App\Modules\IAM\Roles\Requests\SyncRolePermissionsRequest;
use App\Modules\IAM\Roles\Requests\UpdateRoleRequest;
use App\Modules\IAM\Users\Actions\CountUsers;
use App\Modules\IAM\Users\Actions\CreateUser;
use App\Modules\IAM\Users\Actions\DeleteUser;
use App\Modules\IAM\Users\Actions\GetAssignableUsers;
use App\Modules\IAM\Users\Actions\GetUserFilterOptions;
use App\Modules\IAM\Users\Actions\GetUserIndexItems;
use App\Modules\IAM\Users\Actions\IndexUsers;
use App\Modules\IAM\Users\Actions\SyncUserRoles;
use App\Modules\IAM\Users\Actions\UpdateUser;
use App\Modules\IAM\Users\Actions\UserFilterOptionsCatalog;
use App\Modules\IAM\Users\Contracts\UserFilterOptionsProvider;
use App\Modules\IAM\Users\DTOs\AssignableUserData;
use App\Modules\IAM\Users\DTOs\CreateUserData;
use App\Modules\IAM\Users\DTOs\EditableUserData;
use App\Modules\IAM\Users\DTOs\SyncUserRolesData;
use App\Modules\IAM\Users\DTOs\UpdateUserData;
use App\Modules\IAM\Users\DTOs\UserIndexFilterOptionsData;
use App\Modules\IAM\Users\DTOs\UserListItemData;
use App\Modules\IAM\Users\Events\UserCreated;
use App\Modules\IAM\Users\Events\UserDeleted;
use App\Modules\IAM\Users\Events\UserRolesSynced;
use App\Modules\IAM\Users\Events\UserUpdated;
use App\Modules\IAM\Users\Exceptions\CannotDeleteLastSuperAdminUser;
use App\Modules\IAM\Users\Requests\StoreUserRequest;
use App\Modules\IAM\Users\Requests\SyncUserRolesRequest;
use App\Modules\IAM\Users\Requests\UpdateUserRequest;
use App\Modules\Settings\Actions\DeleteProfile;
use App\Modules\Settings\Actions\UpdatePassword;
use App\Modules\Settings\Actions\UpdateProfile;
use App\Modules\Settings\DTOs\UpdateProfileData;
use App\Modules\Settings\Events\PasswordUpdated;
use App\Modules\Settings\Events\ProfileDeleted;
use App\Modules\Settings\Events\ProfileUpdated;
use App\Modules\Settings\Requests\PasswordUpdateRequest;
use App\Modules\Settings\Requests\ProfileDeleteRequest;
use App\Modules\Settings\Requests\ProfileUpdateRequest;
use App\Modules\Settings\Requests\TwoFactorAuthenticationRequest;
use App\Modules\Shared\Actions\AdminIndexQuery;
use App\Modules\Shared\Actions\FormRequestRulesTransformer;
use App\Modules\Shared\Actions\GetAdminIndex;
use App\Modules\Shared\Actions\PasswordValidationRules;
use App\Modules\Shared\Actions\UserIdentityValidationRules;
use App\Modules\Shared\Contracts\AuditableDomainEvent;
use App\Modules\Shared\DTOs\AdminIndexQueryData;
use App\Modules\Shared\Models\User;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
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
    [AdminIndexQuery::class, 'App\\Modules\\Shared\\Actions'],
    [FormRequestRulesTransformer::class, 'App\\Modules\\Shared\\Actions'],
    [PasswordValidationRules::class, 'App\\Modules\\Shared\\Actions'],
    [GetAuthCounts::class, 'App\\Modules\\IAM\\Auth\\Actions'],
    [PermissionFilterOptionsCatalog::class, 'App\\Modules\\IAM\\Permissions\\Actions'],
    [PermissionGroupCatalog::class, 'App\\Modules\\IAM\\Permissions\\Actions'],
    [PermissionNormalizer::class, 'App\\Modules\\IAM\\Permissions\\Actions'],
    [GroupedPermissions::class, 'App\\Modules\\IAM\\Roles\\Actions'],
    [RoleFilterOptionsCatalog::class, 'App\\Modules\\IAM\\Roles\\Actions'],
    [RoleNameNormalizer::class, 'App\\Modules\\IAM\\Roles\\Actions'],
    [UserFilterOptionsCatalog::class, 'App\\Modules\\IAM\\Users\\Actions'],
    [UserIdentityValidationRules::class, 'App\\Modules\\Shared\\Actions'],
    [CannotRenameProtectedSuperAdminRole::class, 'App\\Modules\\IAM\\Roles\\Exceptions'],
    [CannotDeleteProtectedSuperAdminRole::class, 'App\\Modules\\IAM\\Roles\\Exceptions'],
    [CannotRemoveRequiredSuperAdminPermissions::class, 'App\\Modules\\IAM\\Permissions\\Exceptions'],
    [CannotDeleteLastSuperAdminUser::class, 'App\\Modules\\IAM\\Users\\Exceptions'],
    [CannotRemoveLastSuperAdminRoleAssignment::class, 'App\\Modules\\IAM\\Roles\\Exceptions'],
    [UnknownPermissionsSelected::class, 'App\\Modules\\IAM\\Permissions\\Exceptions'],
    [UnknownRolesSelected::class, 'App\\Modules\\IAM\\Roles\\Exceptions'],
]);

dataset('module_contract_classes', [
    [DashboardMetricCounts::class, 'App\\Modules\\Dashboard\\Contracts'],
    [DashboardMetricsProvider::class, 'App\\Modules\\Dashboard\\Contracts'],
    [PermissionGroupCatalogContract::class, 'App\\Modules\\IAM\\Permissions\\Contracts'],
    [PermissionFilterOptionsProvider::class, 'App\\Modules\\IAM\\Permissions\\Contracts'],
    [GroupedPermissionsProvider::class, 'App\\Modules\\IAM\\Roles\\Contracts'],
    [RoleFilterOptionsProvider::class, 'App\\Modules\\IAM\\Roles\\Contracts'],
    [UserFilterOptionsProvider::class, 'App\\Modules\\IAM\\Users\\Contracts'],
]);

dataset('slice_transport_classes', [
    [AuditLogsController::class, 'App\\Http\\Controllers\\Admin'],
    [UsersController::class, 'App\\Http\\Controllers\\Admin'],
    [RolesController::class, 'App\\Http\\Controllers\\Admin'],
    [PermissionsController::class, 'App\\Http\\Controllers\\Admin'],
    [ProfileController::class, 'App\\Http\\Controllers\\Settings'],
    [PasswordController::class, 'App\\Http\\Controllers\\Settings'],
    [TwoFactorAuthenticationController::class, 'App\\Http\\Controllers\\Settings'],
]);

dataset('module_request_classes', [
    [IndexAuditLogsRequest::class, 'App\\Modules\\Audit\\Requests'],
    [StoreUserRequest::class, 'App\\Modules\\IAM\\Users\\Requests'],
    [UpdateUserRequest::class, 'App\\Modules\\IAM\\Users\\Requests'],
    [SyncUserRolesRequest::class, 'App\\Modules\\IAM\\Users\\Requests'],
    [StoreRoleRequest::class, 'App\\Modules\\IAM\\Roles\\Requests'],
    [UpdateRoleRequest::class, 'App\\Modules\\IAM\\Roles\\Requests'],
    [SyncRolePermissionsRequest::class, 'App\\Modules\\IAM\\Roles\\Requests'],
    [StorePermissionRequest::class, 'App\\Modules\\IAM\\Permissions\\Requests'],
    [UpdatePermissionRequest::class, 'App\\Modules\\IAM\\Permissions\\Requests'],
    [ProfileUpdateRequest::class, 'App\\Modules\\Settings\\Requests'],
    [ProfileDeleteRequest::class, 'App\\Modules\\Settings\\Requests'],
    [PasswordUpdateRequest::class, 'App\\Modules\\Settings\\Requests'],
    [TwoFactorAuthenticationRequest::class, 'App\\Modules\\Settings\\Requests'],
]);

dataset('eloquent_metadata_models', [
    [
        User::class,
        ['#[UseFactory(UserFactory::class)]', '#[Fillable([', '#[Hidden(['],
        ['protected $fillable', 'protected $hidden', 'protected static function newFactory'],
    ],
    [
        AuditLog::class,
        ['#[WithoutTimestamps]', '#[Fillable(['],
        ['public $timestamps', 'protected $fillable'],
    ],
    [
        Permission::class,
        ['#[Fillable(['],
        ['protected $fillable'],
    ],
    [
        PermissionGroup::class,
        ['#[Fillable(['],
        ['protected $fillable'],
    ],
]);

it('keeps internal write orchestration on action classes with a public static handle entrypoint', function (string $actionClass): void {
    $reflection = new ReflectionClass($actionClass);

    expect($reflection->getNamespaceName())->toStartWith('App\\Modules\\');
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

    expect($reflection->getNamespaceName())->toStartWith('App\\Modules\\');
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

    expect($reflection->getNamespaceName())->toBe('App\\Modules\\IAM\\Auth\\Actions');
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

    expect($reflection->getNamespaceName())->toStartWith('App\\Modules\\');
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

it('keeps module directories flat and limited to approved directory names', function (): void {
    $allowedDirectories = ['Actions', 'Commands', 'Contracts', 'DTOs', 'Events', 'Exceptions', 'Listeners', 'Models', 'Requests', 'Responses', 'Tests'];
    $moduleDirectories = glob(dirname(__DIR__, 2).'/app/Modules/*', GLOB_ONLYDIR) ?: [];

    expect($moduleDirectories)->not->toBeEmpty();

    foreach ($moduleDirectories as $moduleDirectory) {
        $allowedDirectoriesForModule = basename($moduleDirectory) === 'IAM'
            ? [...$allowedDirectories, 'Auth', 'Permissions', 'Roles', 'Users']
            : $allowedDirectories;
        $childDirectories = glob($moduleDirectory.'/*', GLOB_ONLYDIR) ?: [];

        foreach ($childDirectories as $childDirectory) {
            expect(basename($childDirectory))->toBeIn($allowedDirectoriesForModule);
        }
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

it('keeps the audit logger on a flat module action with a static handle entrypoint', function (): void {
    $reflection = new ReflectionClass(RecordAuditLog::class);

    expect($reflection->getNamespaceName())->toBe('App\\Modules\\Audit\\Actions');
    expect($reflection->isFinal())->toBeTrue();
    expect(backendArchitectureDeclaredPublicMethodNames($reflection))->toContain('handle');

    assertStaticTypedPublicMethods($reflection);
});

it('keeps auditable domain events after-commit and behind shared contracts', function (string $eventClass): void {
    $reflection = new ReflectionClass($eventClass);

    expect($reflection->getNamespaceName())->toStartWith('App\\Modules\\');
    expect($reflection->getNamespaceName())->toContain('\\Events');
    expect($reflection->isFinal())->toBeTrue();
    expect($reflection->implementsInterface(AuditableDomainEvent::class))->toBeTrue();
    expect($reflection->implementsInterface(ShouldDispatchAfterCommit::class))->toBeTrue();
})->with('auditable_event_classes');

it('keeps audit recording behind an interface-driven listener', function (): void {
    $reflection = new ReflectionClass(RecordAuditableDomainEvent::class);
    $contents = file_get_contents($reflection->getFileName());

    expect($reflection->getNamespaceName())->toBe('App\\Modules\\Audit\\Listeners');
    expect($reflection->isFinal())->toBeTrue();
    expect($contents)->toContain('AuditableDomainEvent $event');
    expect($contents)->toContain('RecordAuditLog::handle');
});

it('keeps module listener discovery configured in bootstrap for module-local listeners', function (): void {
    $contents = file_get_contents(dirname(__DIR__, 2).'/bootstrap/app.php');

    expect($contents)->toContain('->withEvents([');
    expect($contents)->toContain("app_path('Listeners')");
    expect($contents)->toContain("app_path('Modules/*/Listeners')");
    expect($contents)->toContain("app_path('Modules/*/*/Listeners')");
});

it('prevents non-audit modules from importing audit actions or models directly', function (): void {
    $projectRoot = dirname(__DIR__, 2);
    $forbiddenSnippets = [
        'use App\\Modules\\Audit\\Actions\\',
        'use App\\Modules\\Audit\\Models\\',
        'RecordAuditLog::handle',
        'ScheduleAuditLog::handle',
    ];
    $moduleDirectories = glob($projectRoot.'/app/Modules/*', GLOB_ONLYDIR) ?: [];

    foreach ($moduleDirectories as $moduleDirectory) {
        if (basename($moduleDirectory) === 'Audit') {
            continue;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($moduleDirectory));

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

it('limits cross-module imports to shared namespaces or explicit contracts', function (): void {
    $projectRoot = dirname(__DIR__, 2);
    $moduleDirectories = glob($projectRoot.'/app/Modules/*', GLOB_ONLYDIR) ?: [];

    foreach ($moduleDirectories as $moduleDirectory) {
        $currentModule = basename($moduleDirectory);
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($moduleDirectory));

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($fileInfo->getPathname());

            preg_match_all('/^use App\\\\Modules\\\\([^\\\\]+)\\\\([^;]+);$/m', $contents, $matches, PREG_SET_ORDER);

            foreach ($matches as [, $targetModule, $targetPath]) {
                if ($targetModule === $currentModule || $targetModule === 'Shared') {
                    continue;
                }

                expect($targetPath)->toStartWith('Contracts\\');
            }
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

    expect($projectRoot.'/app/Modules/IAM/Actions/ProfileValidationRules.php')->not->toBeFile();
    expect($projectRoot.'/app/Modules/Shared/Actions/UserIdentityValidationRules.php')->toBeFile();

    foreach ([
        '/app/Modules/IAM/Users/Commands/CreateUserCommand.php',
        '/app/Modules/IAM/Auth/Actions/ValidateRegistrationInput.php',
        '/app/Modules/IAM/Users/Requests/StoreUserRequest.php',
        '/app/Modules/IAM/Users/Requests/UpdateUserRequest.php',
        '/app/Modules/Settings/Requests/ProfileUpdateRequest.php',
    ] as $path) {
        $contents = file_get_contents($projectRoot.$path);

        expect($contents)->toContain('UserIdentityValidationRules');
        expect($contents)->not->toContain('ProfileValidationRules');
    }
});

it('keeps iam interactive artisan commands inside the iam module', function (): void {
    $projectRoot = dirname(__DIR__, 2);

    foreach ([
        '/app/Modules/IAM/Commands/BaseInteractiveCreateCommand.php',
        '/app/Modules/IAM/Permissions/Commands/CreatePermissionCommand.php',
        '/app/Modules/IAM/Roles/Commands/CreateRoleCommand.php',
        '/app/Modules/IAM/Users/Commands/CreateUserCommand.php',
    ] as $path) {
        expect($projectRoot.$path)->toBeFile();
    }

    foreach ([
        '/app/Console/Commands/BaseInteractiveCreateCommand.php',
        '/app/Console/Commands/CreatePermissionCommand.php',
        '/app/Console/Commands/CreateRoleCommand.php',
        '/app/Console/Commands/CreateUserCommand.php',
        '/app/Modules/IAM/Commands/Create/CreatePermissionCommand.php',
        '/app/Modules/IAM/Commands/Create/CreateRoleCommand.php',
        '/app/Modules/IAM/Commands/Create/CreateUserCommand.php',
    ] as $path) {
        expect($projectRoot.$path)->not->toBeFile();
    }

    $bootstrapContents = file_get_contents($projectRoot.'/bootstrap/app.php');

    expect($bootstrapContents)->toContain('->withCommands([');
    expect($bootstrapContents)->toContain("glob(app_path('Modules/*/Commands'))");
    expect($bootstrapContents)->toContain("glob(app_path('Modules/*/*/Commands'))");
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
