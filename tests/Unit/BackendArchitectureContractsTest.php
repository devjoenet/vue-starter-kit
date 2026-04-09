<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuditLogsController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use App\Modules\Audit\Actions\GetAuditLogFilterOptions;
use App\Modules\Audit\Actions\GetAuditLogIndexItems;
use App\Modules\Audit\Actions\IndexAuditLogs;
use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\DTOs\AuditLogIndexFilterOptionsData;
use App\Modules\Audit\DTOs\AuditLogIndexItemData;
use App\Modules\Audit\DTOs\AuditLogIndexQueryData;
use App\Modules\Audit\Listeners\RecordAuditableDomainEvent;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Audit\Requests\IndexAuditLogsRequest;
use App\Modules\Dashboard\Actions\GetDashboardMetrics;
use App\Modules\Dashboard\Actions\GetDashboardSources;
use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;
use App\Modules\Dashboard\DTOs\DashboardMetricSourceData;
use App\Modules\Dashboard\DTOs\DashboardOverviewSourceData;
use App\Modules\Dashboard\DTOs\DashboardSourcesData;
use App\Modules\IAM\Actions\CreatePermission;
use App\Modules\IAM\Actions\CreateRole;
use App\Modules\IAM\Actions\CreateUser;
use App\Modules\IAM\Actions\DashboardMetrics;
use App\Modules\IAM\Actions\DeletePermission;
use App\Modules\IAM\Actions\DeleteRole;
use App\Modules\IAM\Actions\DeleteUser;
use App\Modules\IAM\Actions\GetAssignableUsers;
use App\Modules\IAM\Actions\GetEditableRoles;
use App\Modules\IAM\Actions\GetPermissionFilterOptions;
use App\Modules\IAM\Actions\GetPermissionIndexItems;
use App\Modules\IAM\Actions\GetRoleFilterOptions;
use App\Modules\IAM\Actions\GetRoleIndexItems;
use App\Modules\IAM\Actions\GetUserFilterOptions;
use App\Modules\IAM\Actions\GetUserIndexItems;
use App\Modules\IAM\Actions\GroupedPermissions;
use App\Modules\IAM\Actions\IndexPermissions;
use App\Modules\IAM\Actions\IndexRoles;
use App\Modules\IAM\Actions\IndexUsers;
use App\Modules\IAM\Actions\PermissionFilterOptionsCatalog;
use App\Modules\IAM\Actions\PermissionGroupCatalog;
use App\Modules\IAM\Actions\PermissionNormalizer;
use App\Modules\IAM\Actions\ProfileValidationRules;
use App\Modules\IAM\Actions\RegisterUser;
use App\Modules\IAM\Actions\ResetUserPassword;
use App\Modules\IAM\Actions\RoleFilterOptionsCatalog;
use App\Modules\IAM\Actions\RoleNameNormalizer;
use App\Modules\IAM\Actions\SyncRolePermissions;
use App\Modules\IAM\Actions\SyncUserRoles;
use App\Modules\IAM\Actions\UpdatePermission;
use App\Modules\IAM\Actions\UpdateRole;
use App\Modules\IAM\Actions\UpdateUser;
use App\Modules\IAM\Actions\UserFilterOptionsCatalog;
use App\Modules\IAM\Contracts\GroupedPermissionsProvider;
use App\Modules\IAM\Contracts\PermissionFilterOptionsProvider;
use App\Modules\IAM\Contracts\PermissionGroupCatalogContract;
use App\Modules\IAM\Contracts\RoleFilterOptionsProvider;
use App\Modules\IAM\Contracts\UserFilterOptionsProvider;
use App\Modules\IAM\DTOs\AssignableUserData;
use App\Modules\IAM\DTOs\AuthenticatedUserData;
use App\Modules\IAM\DTOs\CreatePermissionData;
use App\Modules\IAM\DTOs\CreateRoleData;
use App\Modules\IAM\DTOs\CreateUserData;
use App\Modules\IAM\DTOs\EditableRoleData;
use App\Modules\IAM\DTOs\EditableUserData;
use App\Modules\IAM\DTOs\PermissionGroupOptionData;
use App\Modules\IAM\DTOs\PermissionIndexFilterOptionsData;
use App\Modules\IAM\DTOs\PermissionIndexItemData;
use App\Modules\IAM\DTOs\PermissionItemData;
use App\Modules\IAM\DTOs\RoleIndexFilterOptionsData;
use App\Modules\IAM\DTOs\RoleListItemData;
use App\Modules\IAM\DTOs\RoleOptionData;
use App\Modules\IAM\DTOs\SharedAuthData;
use App\Modules\IAM\DTOs\SyncRolePermissionsData;
use App\Modules\IAM\DTOs\SyncUserRolesData;
use App\Modules\IAM\DTOs\UpdatePermissionData;
use App\Modules\IAM\DTOs\UpdateRoleData;
use App\Modules\IAM\DTOs\UpdateUserData;
use App\Modules\IAM\DTOs\UserIndexFilterOptionsData;
use App\Modules\IAM\DTOs\UserListItemData;
use App\Modules\IAM\Events\PermissionCreated;
use App\Modules\IAM\Events\PermissionDeleted;
use App\Modules\IAM\Events\PermissionUpdated;
use App\Modules\IAM\Events\RoleCreated;
use App\Modules\IAM\Events\RoleDeleted;
use App\Modules\IAM\Events\RolePermissionsSynced;
use App\Modules\IAM\Events\RoleUpdated;
use App\Modules\IAM\Events\UserCreated;
use App\Modules\IAM\Events\UserDeleted;
use App\Modules\IAM\Events\UserRolesSynced;
use App\Modules\IAM\Events\UserUpdated;
use App\Modules\IAM\Exceptions\UnknownPermissionsSelected;
use App\Modules\IAM\Exceptions\UnknownRolesSelected;
use App\Modules\IAM\Models\Permission;
use App\Modules\IAM\Models\PermissionGroup;
use App\Modules\IAM\Requests\StorePermissionRequest;
use App\Modules\IAM\Requests\StoreRoleRequest;
use App\Modules\IAM\Requests\StoreUserRequest;
use App\Modules\IAM\Requests\SyncRolePermissionsRequest;
use App\Modules\IAM\Requests\SyncUserRolesRequest;
use App\Modules\IAM\Requests\UpdatePermissionRequest;
use App\Modules\IAM\Requests\UpdateRoleRequest;
use App\Modules\IAM\Requests\UpdateUserRequest;
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
    IndexAuditLogs::class,
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
    [DashboardMetrics::class, 'App\\Modules\\IAM\\Actions'],
    [PermissionFilterOptionsCatalog::class, 'App\\Modules\\IAM\\Actions'],
    [PermissionGroupCatalog::class, 'App\\Modules\\IAM\\Actions'],
    [PermissionNormalizer::class, 'App\\Modules\\IAM\\Actions'],
    [GroupedPermissions::class, 'App\\Modules\\IAM\\Actions'],
    [RoleFilterOptionsCatalog::class, 'App\\Modules\\IAM\\Actions'],
    [RoleNameNormalizer::class, 'App\\Modules\\IAM\\Actions'],
    [UserFilterOptionsCatalog::class, 'App\\Modules\\IAM\\Actions'],
    [ProfileValidationRules::class, 'App\\Modules\\IAM\\Actions'],
    [UnknownPermissionsSelected::class, 'App\\Modules\\IAM\\Exceptions'],
    [UnknownRolesSelected::class, 'App\\Modules\\IAM\\Exceptions'],
]);

dataset('module_contract_classes', [
    [DashboardMetricsProvider::class, 'App\\Modules\\Dashboard\\Contracts'],
    [PermissionGroupCatalogContract::class, 'App\\Modules\\IAM\\Contracts'],
    [PermissionFilterOptionsProvider::class, 'App\\Modules\\IAM\\Contracts'],
    [GroupedPermissionsProvider::class, 'App\\Modules\\IAM\\Contracts'],
    [RoleFilterOptionsProvider::class, 'App\\Modules\\IAM\\Contracts'],
    [UserFilterOptionsProvider::class, 'App\\Modules\\IAM\\Contracts'],
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
    [StoreUserRequest::class, 'App\\Modules\\IAM\\Requests'],
    [UpdateUserRequest::class, 'App\\Modules\\IAM\\Requests'],
    [SyncUserRolesRequest::class, 'App\\Modules\\IAM\\Requests'],
    [StoreRoleRequest::class, 'App\\Modules\\IAM\\Requests'],
    [UpdateRoleRequest::class, 'App\\Modules\\IAM\\Requests'],
    [SyncRolePermissionsRequest::class, 'App\\Modules\\IAM\\Requests'],
    [StorePermissionRequest::class, 'App\\Modules\\IAM\\Requests'],
    [UpdatePermissionRequest::class, 'App\\Modules\\IAM\\Requests'],
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

    expect($reflection->getNamespaceName())->toBe('App\\Modules\\IAM\\Actions');
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
        $childDirectories = glob($moduleDirectory.'/*', GLOB_ONLYDIR) ?: [];

        foreach ($childDirectories as $childDirectory) {
            expect(basename($childDirectory))->toBeIn($allowedDirectories);
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
