<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\SyncRolePermissionsRequest;
use App\Http\Requests\Admin\SyncUserRolesRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use App\Modules\Audit\Actions\RecordAuditLog;
use App\Modules\Audit\Models\AuditLog;
use App\Modules\Dashboard\Actions\DashboardMetrics;
use App\Modules\Dashboard\Actions\GetDashboardMetrics;
use App\Modules\Dashboard\Actions\GetDashboardSources;
use App\Modules\Dashboard\Contracts\DashboardMetricsProvider;
use App\Modules\Dashboard\DTOs\DashboardMetricSourceData;
use App\Modules\Dashboard\DTOs\DashboardOverviewSourceData;
use App\Modules\Dashboard\DTOs\DashboardSourcesData;
use App\Modules\Permissions\Actions\CreatePermission;
use App\Modules\Permissions\Actions\DeletePermission;
use App\Modules\Permissions\Actions\GetPermissionFilterOptions;
use App\Modules\Permissions\Actions\GetPermissionIndexItems;
use App\Modules\Permissions\Actions\IndexPermissions;
use App\Modules\Permissions\Actions\PermissionFilterOptionsCatalog;
use App\Modules\Permissions\Actions\PermissionGroupCatalog;
use App\Modules\Permissions\Actions\PermissionNormalizer;
use App\Modules\Permissions\Actions\UpdatePermission;
use App\Modules\Permissions\Contracts\PermissionFilterOptionsProvider;
use App\Modules\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\Permissions\DTOs\CreatePermissionData;
use App\Modules\Permissions\DTOs\PermissionGroupOptionData;
use App\Modules\Permissions\DTOs\PermissionIndexFilterOptionsData;
use App\Modules\Permissions\DTOs\PermissionIndexItemData;
use App\Modules\Permissions\DTOs\PermissionItemData;
use App\Modules\Permissions\DTOs\UpdatePermissionData;
use App\Modules\Permissions\Models\Permission;
use App\Modules\Permissions\Models\PermissionGroup;
use App\Modules\Roles\Actions\CreateRole;
use App\Modules\Roles\Actions\DeleteRole;
use App\Modules\Roles\Actions\GetAssignableUsers;
use App\Modules\Roles\Actions\GetRoleFilterOptions;
use App\Modules\Roles\Actions\GetRoleIndexItems;
use App\Modules\Roles\Actions\GroupedPermissions;
use App\Modules\Roles\Actions\IndexRoles;
use App\Modules\Roles\Actions\RoleFilterOptionsCatalog;
use App\Modules\Roles\Actions\RoleNameNormalizer;
use App\Modules\Roles\Actions\SyncRolePermissions;
use App\Modules\Roles\Actions\UpdateRole;
use App\Modules\Roles\Contracts\GroupedPermissionsProvider;
use App\Modules\Roles\Contracts\RoleFilterOptionsProvider;
use App\Modules\Roles\DTOs\AssignableUserData;
use App\Modules\Roles\DTOs\CreateRoleData;
use App\Modules\Roles\DTOs\EditableRoleData;
use App\Modules\Roles\DTOs\RoleIndexFilterOptionsData;
use App\Modules\Roles\DTOs\RoleListItemData;
use App\Modules\Roles\DTOs\SyncRolePermissionsData;
use App\Modules\Roles\DTOs\UpdateRoleData;
use App\Modules\Roles\Exceptions\UnknownPermissionsSelected;
use App\Modules\Settings\Actions\DeleteProfile;
use App\Modules\Settings\Actions\UpdatePassword;
use App\Modules\Settings\Actions\UpdateProfile;
use App\Modules\Settings\DTOs\UpdateProfileData;
use App\Modules\Shared\Actions\AdminIndexQuery;
use App\Modules\Shared\Actions\FormRequestRulesTransformer;
use App\Modules\Shared\Actions\GetAdminIndex;
use App\Modules\Shared\Actions\PasswordValidationRules;
use App\Modules\Shared\DTOs\AdminIndexQueryData;
use App\Modules\Users\Actions\CreateUser;
use App\Modules\Users\Actions\DeleteUser;
use App\Modules\Users\Actions\GetEditableRoles;
use App\Modules\Users\Actions\GetUserFilterOptions;
use App\Modules\Users\Actions\GetUserIndexItems;
use App\Modules\Users\Actions\IndexUsers;
use App\Modules\Users\Actions\ProfileValidationRules;
use App\Modules\Users\Actions\RegisterUser;
use App\Modules\Users\Actions\ResetUserPassword;
use App\Modules\Users\Actions\SyncUserRoles;
use App\Modules\Users\Actions\UpdateUser;
use App\Modules\Users\Actions\UserFilterOptionsCatalog;
use App\Modules\Users\Contracts\UserFilterOptionsProvider;
use App\Modules\Users\DTOs\AuthenticatedUserData;
use App\Modules\Users\DTOs\CreateUserData;
use App\Modules\Users\DTOs\EditableUserData;
use App\Modules\Users\DTOs\RoleOptionData;
use App\Modules\Users\DTOs\SharedAuthData;
use App\Modules\Users\DTOs\SyncUserRolesData;
use App\Modules\Users\DTOs\UpdateUserData;
use App\Modules\Users\DTOs\UserIndexFilterOptionsData;
use App\Modules\Users\DTOs\UserListItemData;
use App\Modules\Users\Exceptions\UnknownRolesSelected;
use App\Modules\Users\Models\User;
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

dataset('backend_data_classes', [
    AdminIndexQueryData::class,
    DashboardMetricSourceData::class,
    DashboardOverviewSourceData::class,
    DashboardSourcesData::class,
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
    [DashboardMetrics::class, 'App\\Modules\\Dashboard\\Actions'],
    [AdminIndexQuery::class, 'App\\Modules\\Shared\\Actions'],
    [FormRequestRulesTransformer::class, 'App\\Modules\\Shared\\Actions'],
    [PasswordValidationRules::class, 'App\\Modules\\Shared\\Actions'],
    [PermissionFilterOptionsCatalog::class, 'App\\Modules\\Permissions\\Actions'],
    [PermissionGroupCatalog::class, 'App\\Modules\\Permissions\\Actions'],
    [PermissionNormalizer::class, 'App\\Modules\\Permissions\\Actions'],
    [GroupedPermissions::class, 'App\\Modules\\Roles\\Actions'],
    [RoleFilterOptionsCatalog::class, 'App\\Modules\\Roles\\Actions'],
    [RoleNameNormalizer::class, 'App\\Modules\\Roles\\Actions'],
    [UserFilterOptionsCatalog::class, 'App\\Modules\\Users\\Actions'],
    [ProfileValidationRules::class, 'App\\Modules\\Users\\Actions'],
    [UnknownPermissionsSelected::class, 'App\\Modules\\Roles\\Exceptions'],
    [UnknownRolesSelected::class, 'App\\Modules\\Users\\Exceptions'],
]);

dataset('module_contract_classes', [
    [DashboardMetricsProvider::class, 'App\\Modules\\Dashboard\\Contracts'],
    [PermissionGroupCatalogContract::class, 'App\\Modules\\Permissions\\Contracts'],
    [PermissionFilterOptionsProvider::class, 'App\\Modules\\Permissions\\Contracts'],
    [GroupedPermissionsProvider::class, 'App\\Modules\\Roles\\Contracts'],
    [RoleFilterOptionsProvider::class, 'App\\Modules\\Roles\\Contracts'],
    [UserFilterOptionsProvider::class, 'App\\Modules\\Users\\Contracts'],
]);

dataset('slice_transport_classes', [
    [UsersController::class, 'App\\Http\\Controllers\\Admin'],
    [StoreUserRequest::class, 'App\\Http\\Requests\\Admin'],
    [UpdateUserRequest::class, 'App\\Http\\Requests\\Admin'],
    [SyncUserRolesRequest::class, 'App\\Http\\Requests\\Admin'],
    [RolesController::class, 'App\\Http\\Controllers\\Admin'],
    [StoreRoleRequest::class, 'App\\Http\\Requests\\Admin'],
    [UpdateRoleRequest::class, 'App\\Http\\Requests\\Admin'],
    [SyncRolePermissionsRequest::class, 'App\\Http\\Requests\\Admin'],
    [PermissionsController::class, 'App\\Http\\Controllers\\Admin'],
    [StorePermissionRequest::class, 'App\\Http\\Requests\\Admin'],
    [UpdatePermissionRequest::class, 'App\\Http\\Requests\\Admin'],
    [ProfileController::class, 'App\\Http\\Controllers\\Settings'],
    [ProfileUpdateRequest::class, 'App\\Http\\Requests\\Settings'],
    [ProfileDeleteRequest::class, 'App\\Http\\Requests\\Settings'],
    [PasswordController::class, 'App\\Http\\Controllers\\Settings'],
    [PasswordUpdateRequest::class, 'App\\Http\\Requests\\Settings'],
    [TwoFactorAuthenticationController::class, 'App\\Http\\Controllers\\Settings'],
    [TwoFactorAuthenticationRequest::class, 'App\\Http\\Requests\\Settings'],
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

it('keeps write actions transactional and records durable audit entries after commit', function (string $actionClass): void {
    $reflection = new ReflectionClass($actionClass);
    $contents = file_get_contents($reflection->getFileName());

    expect($contents)->toContain('DB::transaction');
    expect($contents)->toContain('DB::afterCommit');
    expect($contents)->toContain('RecordAuditLog::handle');
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

    expect($reflection->getNamespaceName())->toBe('App\\Modules\\Users\\Actions');
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
    $allowedDirectories = ['Actions', 'Commands', 'Contracts', 'DTOs', 'Exceptions', 'Models'];
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
