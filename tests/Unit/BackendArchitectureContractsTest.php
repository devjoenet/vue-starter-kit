<?php

declare(strict_types=1);

use App\Http\Admin\Permissions\Controllers\PermissionsController;
use App\Http\Admin\Permissions\Requests\StorePermissionRequest;
use App\Http\Admin\Permissions\Requests\UpdatePermissionRequest;
use App\Http\Admin\Roles\Controllers\RolesController;
use App\Http\Admin\Roles\Requests\StoreRoleRequest;
use App\Http\Admin\Roles\Requests\SyncRolePermissionsRequest;
use App\Http\Admin\Roles\Requests\UpdateRoleRequest;
use App\Http\Admin\Users\Controllers\UsersController;
use App\Http\Admin\Users\Requests\StoreUserRequest;
use App\Http\Admin\Users\Requests\SyncUserRolesRequest;
use App\Http\Admin\Users\Requests\UpdateUserRequest;
use App\Http\Settings\Password\Controllers\PasswordController;
use App\Http\Settings\Password\Requests\PasswordUpdateRequest;
use App\Http\Settings\Profile\Controllers\ProfileController;
use App\Http\Settings\Profile\Requests\ProfileDeleteRequest;
use App\Http\Settings\Profile\Requests\ProfileUpdateRequest;
use App\Http\Settings\TwoFactor\Controllers\TwoFactorAuthenticationController;
use App\Http\Settings\TwoFactor\Requests\TwoFactorAuthenticationRequest;
use App\Modules\Admin\Permissions\Actions\CreatePermission;
use App\Modules\Admin\Permissions\Actions\DeletePermission;
use App\Modules\Admin\Permissions\Actions\UpdatePermission;
use App\Modules\Admin\Permissions\DTOs\CreatePermissionData;
use App\Modules\Admin\Permissions\DTOs\PermissionGroupOptionData;
use App\Modules\Admin\Permissions\DTOs\PermissionIndexItemData;
use App\Modules\Admin\Permissions\DTOs\PermissionItemData;
use App\Modules\Admin\Permissions\DTOs\UpdatePermissionData;
use App\Modules\Admin\Permissions\Queries\GetPermissionFilterOptions;
use App\Modules\Admin\Permissions\Queries\GetPermissionIndexItems;
use App\Modules\Admin\Permissions\Queries\IndexPermissions;
use App\Modules\Admin\Permissions\Support\PermissionGroupCatalog;
use App\Modules\Admin\Permissions\Support\PermissionNormalizer;
use App\Modules\Admin\Roles\Actions\CreateRole;
use App\Modules\Admin\Roles\Actions\DeleteRole;
use App\Modules\Admin\Roles\Actions\SyncRolePermissions;
use App\Modules\Admin\Roles\Actions\UpdateRole;
use App\Modules\Admin\Roles\DTOs\AssignableUserData;
use App\Modules\Admin\Roles\DTOs\CreateRoleData;
use App\Modules\Admin\Roles\DTOs\EditableRoleData;
use App\Modules\Admin\Roles\DTOs\RoleListItemData;
use App\Modules\Admin\Roles\DTOs\SyncRolePermissionsData;
use App\Modules\Admin\Roles\DTOs\UpdateRoleData;
use App\Modules\Admin\Roles\Exceptions\UnknownPermissionsSelected;
use App\Modules\Admin\Roles\Queries\GetAssignableUsers;
use App\Modules\Admin\Roles\Queries\GetRoleFilterOptions;
use App\Modules\Admin\Roles\Queries\GetRoleIndexItems;
use App\Modules\Admin\Roles\Queries\IndexRoles;
use App\Modules\Admin\Roles\Support\GroupedPermissions;
use App\Modules\Admin\Roles\Support\RoleNameNormalizer;
use App\Modules\Admin\Shared\DTOs\AdminIndexQueryData;
use App\Modules\Admin\Shared\Queries\GetAdminIndex;
use App\Modules\Admin\Shared\Support\AdminIndexQuery;
use App\Modules\Admin\Users\Actions\CreateUser;
use App\Modules\Admin\Users\Actions\DeleteUser;
use App\Modules\Admin\Users\Actions\SyncUserRoles;
use App\Modules\Admin\Users\Actions\UpdateUser;
use App\Modules\Admin\Users\DTOs\CreateUserData;
use App\Modules\Admin\Users\DTOs\EditableUserData;
use App\Modules\Admin\Users\DTOs\RoleOptionData;
use App\Modules\Admin\Users\DTOs\SyncUserRolesData;
use App\Modules\Admin\Users\DTOs\UpdateUserData;
use App\Modules\Admin\Users\DTOs\UserListItemData;
use App\Modules\Admin\Users\Exceptions\UnknownRolesSelected;
use App\Modules\Admin\Users\Queries\GetEditableRoles;
use App\Modules\Admin\Users\Queries\GetUserFilterOptions;
use App\Modules\Admin\Users\Queries\GetUserIndexItems;
use App\Modules\Admin\Users\Queries\IndexUsers;
use App\Modules\Auth\Actions\CreateNewUser;
use App\Modules\Auth\Actions\ResetUserPassword;
use App\Modules\Auth\DTOs\AuthenticatedUserData;
use App\Modules\Auth\DTOs\SharedAuthData;
use App\Modules\Settings\Actions\DeleteProfile;
use App\Modules\Settings\Actions\UpdatePassword;
use App\Modules\Settings\Actions\UpdateProfile;
use App\Modules\Settings\DTOs\UpdateProfileData;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use Spatie\LaravelData\Data;

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

dataset('project_query_classes', [
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

dataset('fortify_action_adapters', [
    [CreateNewUser::class, CreatesNewUsers::class, 'create'],
    [ResetUserPassword::class, ResetsUserPasswords::class, 'reset'],
]);

dataset('backend_data_classes', [
    AdminIndexQueryData::class,
    CreateUserData::class,
    UpdateUserData::class,
    EditableUserData::class,
    RoleOptionData::class,
    SyncUserRolesData::class,
    UserListItemData::class,
    CreateRoleData::class,
    UpdateRoleData::class,
    EditableRoleData::class,
    AssignableUserData::class,
    RoleListItemData::class,
    SyncRolePermissionsData::class,
    CreatePermissionData::class,
    UpdatePermissionData::class,
    PermissionItemData::class,
    PermissionIndexItemData::class,
    PermissionGroupOptionData::class,
    AuthenticatedUserData::class,
    SharedAuthData::class,
    UpdateProfileData::class,
]);

dataset('module_support_classes', [
    [AdminIndexQuery::class, 'App\\Modules\\Admin\\Shared\\Support'],
    [PermissionGroupCatalog::class, 'App\\Modules\\Admin\\Permissions\\Support'],
    [PermissionNormalizer::class, 'App\\Modules\\Admin\\Permissions\\Support'],
    [GroupedPermissions::class, 'App\\Modules\\Admin\\Roles\\Support'],
    [RoleNameNormalizer::class, 'App\\Modules\\Admin\\Roles\\Support'],
    [UnknownPermissionsSelected::class, 'App\\Modules\\Admin\\Roles\\Exceptions'],
    [UnknownRolesSelected::class, 'App\\Modules\\Admin\\Users\\Exceptions'],
]);

dataset('slice_transport_classes', [
    [UsersController::class, 'App\\Http\\Admin\\Users\\Controllers'],
    [StoreUserRequest::class, 'App\\Http\\Admin\\Users\\Requests'],
    [UpdateUserRequest::class, 'App\\Http\\Admin\\Users\\Requests'],
    [SyncUserRolesRequest::class, 'App\\Http\\Admin\\Users\\Requests'],
    [RolesController::class, 'App\\Http\\Admin\\Roles\\Controllers'],
    [StoreRoleRequest::class, 'App\\Http\\Admin\\Roles\\Requests'],
    [UpdateRoleRequest::class, 'App\\Http\\Admin\\Roles\\Requests'],
    [SyncRolePermissionsRequest::class, 'App\\Http\\Admin\\Roles\\Requests'],
    [PermissionsController::class, 'App\\Http\\Admin\\Permissions\\Controllers'],
    [StorePermissionRequest::class, 'App\\Http\\Admin\\Permissions\\Requests'],
    [UpdatePermissionRequest::class, 'App\\Http\\Admin\\Permissions\\Requests'],
    [ProfileController::class, 'App\\Http\\Settings\\Profile\\Controllers'],
    [ProfileUpdateRequest::class, 'App\\Http\\Settings\\Profile\\Requests'],
    [ProfileDeleteRequest::class, 'App\\Http\\Settings\\Profile\\Requests'],
    [PasswordController::class, 'App\\Http\\Settings\\Password\\Controllers'],
    [PasswordUpdateRequest::class, 'App\\Http\\Settings\\Password\\Requests'],
    [TwoFactorAuthenticationController::class, 'App\\Http\\Settings\\TwoFactor\\Controllers'],
    [TwoFactorAuthenticationRequest::class, 'App\\Http\\Settings\\TwoFactor\\Requests'],
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

it('keeps read-side queries on classes with a public static handle entrypoint', function (string $queryClass): void {
    $reflection = new ReflectionClass($queryClass);

    expect($reflection->getNamespaceName())->toStartWith('App\\Modules\\');
    expect($reflection->getNamespaceName())->toContain('\\Queries');
    expect($reflection->hasMethod('__invoke'))->toBeFalse();
    expect(backendArchitectureDeclaredPublicMethodNames($reflection))->toContain('handle');

    assertStaticTypedPublicMethods($reflection);
})->with('project_query_classes');

it('keeps read-side query handlers small enough for focused orchestration', function (string $queryClass): void {
    $reflection = new ReflectionClass($queryClass);
    $handleMethod = $reflection->getMethod('handle');

    expect(backendArchitectureMethodLineCount($handleMethod))->toBeLessThanOrEqual(30);
})->with('project_query_classes');

it('keeps Fortify adapter actions on their vendor contract methods instead of the project handle api', function (
    string $actionClass,
    string $contract,
    string $contractMethod,
): void {
    $reflection = new ReflectionClass($actionClass);

    expect($reflection->getNamespaceName())->toBe('App\\Modules\\Auth\\Actions');
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

it('keeps shared and slice-specific support collaborators in module-owned namespaces', function (
    string $className,
    string $expectedNamespace,
): void {
    $reflection = new ReflectionClass($className);

    expect($reflection->getNamespaceName())->toBe($expectedNamespace);
})->with('module_support_classes');

it('keeps admin and settings transport classes in slice-oriented http namespaces', function (
    string $className,
    string $expectedNamespace,
): void {
    $reflection = new ReflectionClass($className);

    expect($reflection->getNamespaceName())->toBe($expectedNamespace);
})->with('slice_transport_classes');

/**
 * @return list<string>
 */
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

/**
 * @return list<ReflectionMethod>
 */
function backendArchitectureDeclaredPublicMethods(ReflectionClass $reflection): array
{
    return array_values(array_filter(
        $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
        fn (ReflectionMethod $method): bool => $method->getDeclaringClass()->getName() === $reflection->getName(),
    ));
}
