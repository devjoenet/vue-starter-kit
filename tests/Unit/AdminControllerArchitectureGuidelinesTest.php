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
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Http\RedirectResponse;

dataset('admin_controller_classes', [
    [UsersController::class, ['create', 'destroy', 'edit', 'index', 'store', 'syncRoles', 'update']],
    [RolesController::class, ['create', 'destroy', 'edit', 'index', 'store', 'syncPermissions', 'update']],
    [PermissionsController::class, ['__construct', 'create', 'destroy', 'edit', 'index', 'store', 'update']],
]);

dataset('admin_controller_endpoint_methods', [
    [UsersController::class, 'index'],
    [UsersController::class, 'create'],
    [UsersController::class, 'store'],
    [UsersController::class, 'edit'],
    [UsersController::class, 'update'],
    [UsersController::class, 'destroy'],
    [UsersController::class, 'syncRoles'],
    [RolesController::class, 'index'],
    [RolesController::class, 'create'],
    [RolesController::class, 'store'],
    [RolesController::class, 'edit'],
    [RolesController::class, 'update'],
    [RolesController::class, 'destroy'],
    [RolesController::class, 'syncPermissions'],
    [PermissionsController::class, 'index'],
    [PermissionsController::class, 'create'],
    [PermissionsController::class, 'store'],
    [PermissionsController::class, 'edit'],
    [PermissionsController::class, 'update'],
    [PermissionsController::class, 'destroy'],
]);

dataset('admin_controller_write_endpoints', [
    [UsersController::class, 'store', CreateUser::class],
    [UsersController::class, 'update', UpdateUser::class],
    [UsersController::class, 'destroy', DeleteUser::class],
    [UsersController::class, 'syncRoles', SyncUserRoles::class],
    [RolesController::class, 'store', CreateRole::class],
    [RolesController::class, 'update', UpdateRole::class],
    [RolesController::class, 'destroy', DeleteRole::class],
    [RolesController::class, 'syncPermissions', SyncRolePermissions::class],
    [PermissionsController::class, 'store', CreatePermission::class],
    [PermissionsController::class, 'update', UpdatePermission::class],
    [PermissionsController::class, 'destroy', DeletePermission::class],
]);

it('keeps admin controllers final and limited to route endpoint methods', function (
    string $controllerClass,
    array $allowedMethods,
): void {
    $reflection = new ReflectionClass($controllerClass);

    expect($reflection->isFinal())->toBeTrue();
    expect(adminControllerDeclaredPublicMethodNames($reflection))->toBe($allowedMethods);
})->with('admin_controller_classes');

it('keeps admin controller endpoints under thirty lines', function (string $controllerClass, string $methodName): void {
    $reflection = new ReflectionClass($controllerClass);
    $method = $reflection->getMethod($methodName);

    expect(adminControllerMethodLineCount($method))->toBeLessThanOrEqual(30);
})->with('admin_controller_endpoint_methods');

it('keeps admin write endpoints delegated to action classes', function (
    string $controllerClass,
    string $methodName,
    string $actionClass,
): void {
    $reflection = new ReflectionClass($controllerClass);
    $method = $reflection->getMethod($methodName);

    expect((string) $method->getReturnType())->toBe(RedirectResponse::class);
    expect(adminControllerParameterTypes($method))->not->toContain($actionClass);
    expect(adminControllerMethodBody($method))->toContain(adminControllerStaticHandleCall($actionClass));
})->with('admin_controller_write_endpoints');

/**
 * @return list<string>
 */
function adminControllerDeclaredPublicMethodNames(ReflectionClass $reflection): array
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

function adminControllerMethodLineCount(ReflectionMethod $method): int
{
    return $method->getEndLine() - $method->getStartLine() + 1;
}

/**
 * @return list<string>
 */
function adminControllerParameterTypes(ReflectionMethod $method): array
{
    return array_values(array_filter(array_map(
        function (ReflectionParameter $parameter): ?string {
            $type = $parameter->getType();

            if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
                return null;
            }

            return $type->getName();
        },
        $method->getParameters(),
    )));
}

function adminControllerMethodBody(ReflectionMethod $method): string
{
    $fileName = $method->getFileName();

    if ($fileName === false) {
        return '';
    }

    $lines = file($fileName, FILE_IGNORE_NEW_LINES);

    if ($lines === false) {
        return '';
    }

    return implode("\n", array_slice(
        $lines,
        $method->getStartLine() - 1,
        $method->getEndLine() - $method->getStartLine() + 1,
    ));
}

function adminControllerStaticHandleCall(string $actionClass): string
{
    return sprintf('%s::handle(', basename(str_replace('\\', '/', $actionClass)));
}
