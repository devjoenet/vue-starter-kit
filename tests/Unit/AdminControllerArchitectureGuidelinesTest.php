<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuditLogsController;
use App\Http\Controllers\Admin\PermissionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use App\Modules\Audit\Actions\GetAuditHistoryItems;
use App\Modules\Audit\Actions\IndexAuditLogs;
use App\Modules\IAM\Permissions\Actions\CreatePermission;
use App\Modules\IAM\Permissions\Actions\DeletePermission;
use App\Modules\IAM\Permissions\Actions\IndexPermissions;
use App\Modules\IAM\Permissions\Actions\UpdatePermission;
use App\Modules\IAM\Roles\Actions\CreateRole;
use App\Modules\IAM\Roles\Actions\DeleteRole;
use App\Modules\IAM\Roles\Actions\GetEditableRoles;
use App\Modules\IAM\Roles\Actions\IndexRoles;
use App\Modules\IAM\Roles\Actions\SyncRolePermissions;
use App\Modules\IAM\Roles\Actions\UpdateRole;
use App\Modules\IAM\Roles\Contracts\GroupedPermissionsProvider;
use App\Modules\IAM\Users\Actions\CreateUser;
use App\Modules\IAM\Users\Actions\DeleteUser;
use App\Modules\IAM\Users\Actions\GetAssignableUsers;
use App\Modules\IAM\Users\Actions\IndexUsers;
use App\Modules\IAM\Users\Actions\SyncUserRoles;
use App\Modules\IAM\Users\Actions\UpdateUser;
use Illuminate\Http\RedirectResponse;

dataset('admin_controller_classes', [
    [AuditLogsController::class, ['index']],
    [UsersController::class, ['create', 'destroy', 'edit', 'index', 'store', 'syncRoles', 'update']],
    [RolesController::class, ['create', 'destroy', 'edit', 'index', 'store', 'syncPermissions', 'update']],
    [PermissionsController::class, ['__construct', 'create', 'destroy', 'edit', 'index', 'store', 'update']],
]);

dataset('admin_controller_endpoint_methods', [
    [AuditLogsController::class, 'index'],
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

dataset('admin_controller_read_action_endpoints', [
    [AuditLogsController::class, 'index', IndexAuditLogs::class],
    [UsersController::class, 'index', IndexUsers::class],
    [UsersController::class, 'edit', GetEditableRoles::class],
    [UsersController::class, 'edit', GetAuditHistoryItems::class],
    [RolesController::class, 'index', IndexRoles::class],
    [RolesController::class, 'create', GetAssignableUsers::class],
    [RolesController::class, 'edit', GetAuditHistoryItems::class],
    [PermissionsController::class, 'index', IndexPermissions::class],
    [PermissionsController::class, 'edit', GetAuditHistoryItems::class],
]);

dataset('admin_controller_injected_read_endpoints', [
    [RolesController::class, 'edit', GroupedPermissionsProvider::class, 'allData'],
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

it('keeps admin read endpoints delegated to explicit read-side actions', function (
    string $controllerClass,
    string $methodName,
    string $actionClass,
): void {
    $reflection = new ReflectionClass($controllerClass);
    $method = $reflection->getMethod($methodName);

    expect(adminControllerParameterTypes($method))->not->toContain($actionClass);
    expect(adminControllerMethodBody($method))->toContain(adminControllerStaticHandleCall($actionClass));
})->with('admin_controller_read_action_endpoints');

it('keeps admin deferred edit payloads delegated to injected read-side collaborators', function (
    string $controllerClass,
    string $methodName,
    string $collaboratorClass,
    string $methodCall,
): void {
    $reflection = new ReflectionClass($controllerClass);
    $method = $reflection->getMethod($methodName);

    expect(adminControllerParameterTypes($method))->toContain($collaboratorClass);
    expect(adminControllerMethodBody($method))->toContain(adminControllerCollaboratorCall($methodCall));
})->with('admin_controller_injected_read_endpoints');

/** @return list<string> */
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

/** @return list<string> */
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

function adminControllerCollaboratorCall(string $methodCall): string
{
    return sprintf('->%s(', $methodCall);
}
