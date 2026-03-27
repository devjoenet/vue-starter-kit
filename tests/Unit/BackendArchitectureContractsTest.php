<?php

declare(strict_types=1);

use App\Actions\Admin\Permissions\CreatePermission;
use App\Actions\Admin\Permissions\DeletePermission;
use App\Actions\Admin\Permissions\UpdatePermission;
use App\Actions\Admin\Roles\CreateRole;
use App\Actions\Admin\Roles\DeleteRole;
use App\Actions\Admin\Roles\SyncRolePermissions;
use App\Actions\Admin\Roles\UpdateRole;
use App\Actions\Admin\Users\CreateUser;
use App\Actions\Admin\Users\DeleteUser;
use App\Actions\Admin\Users\SyncUserRoles;
use App\Actions\Admin\Users\UpdateUser;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Settings\DeleteProfile;
use App\Actions\Settings\UpdatePassword;
use App\Actions\Settings\UpdateProfile;
use App\Support\Data\Admin\Permissions\CreatePermissionData;
use App\Support\Data\Admin\Permissions\PermissionGroupOptionData;
use App\Support\Data\Admin\Permissions\PermissionIndexItemData;
use App\Support\Data\Admin\Permissions\PermissionItemData;
use App\Support\Data\Admin\Permissions\UpdatePermissionData;
use App\Support\Data\Admin\Roles\AssignableUserData;
use App\Support\Data\Admin\Roles\CreateRoleData;
use App\Support\Data\Admin\Roles\EditableRoleData;
use App\Support\Data\Admin\Roles\RoleListItemData;
use App\Support\Data\Admin\Roles\SyncRolePermissionsData;
use App\Support\Data\Admin\Roles\UpdateRoleData;
use App\Support\Data\Admin\Users\CreateUserData;
use App\Support\Data\Admin\Users\EditableUserData;
use App\Support\Data\Admin\Users\RoleOptionData;
use App\Support\Data\Admin\Users\SyncUserRolesData;
use App\Support\Data\Admin\Users\UpdateUserData;
use App\Support\Data\Admin\Users\UserListItemData;
use App\Support\Data\Auth\AuthenticatedUserData;
use App\Support\Data\Auth\SharedAuthData;
use App\Support\Data\Settings\UpdateProfileData;
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

dataset('fortify_action_adapters', [
    [CreateNewUser::class, CreatesNewUsers::class, 'create'],
    [ResetUserPassword::class, ResetsUserPasswords::class, 'reset'],
]);

dataset('backend_data_classes', [
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

it('keeps internal write orchestration on final action classes with a single typed handle method', function (string $actionClass): void {
    $reflection = new ReflectionClass($actionClass);
    $publicMethods = backendArchitectureDeclaredPublicMethodNames($reflection);

    expect($reflection->isFinal())->toBeTrue();
    expect($reflection->hasMethod('__invoke'))->toBeFalse();
    expect($reflection->hasMethod('handle'))->toBeTrue();
    expect(array_diff($publicMethods, ['__construct', 'handle']))->toBe([]);

    /** @var ReflectionMethod $handleMethod */
    $handleMethod = $reflection->getMethod('handle');

    expect($handleMethod->isPublic())->toBeTrue();
    expect($handleMethod->hasReturnType())->toBeTrue();

    foreach ($handleMethod->getParameters() as $parameter) {
        expect($parameter->hasType())->toBeTrue();
    }
})->with('project_write_action_classes');

it('keeps internal write actions small enough for single-purpose handle methods', function (string $actionClass): void {
    $reflection = new ReflectionClass($actionClass);
    $handleMethod = $reflection->getMethod('handle');

    expect(backendArchitectureMethodLineCount($handleMethod))->toBeLessThanOrEqual(30);
})->with('project_write_action_classes');

it('keeps Fortify adapter actions on their vendor contract methods instead of the project handle api', function (
    string $actionClass,
    string $contract,
    string $contractMethod,
): void {
    $reflection = new ReflectionClass($actionClass);

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

    expect($reflection->isFinal())->toBeTrue();
    expect($reflection->isSubclassOf(Data::class))->toBeTrue();
})->with('backend_data_classes');

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
