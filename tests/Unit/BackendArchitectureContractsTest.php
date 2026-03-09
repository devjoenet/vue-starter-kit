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
use App\Actions\Settings\DeleteProfile;
use App\Actions\Settings\UpdatePassword;
use App\Actions\Settings\UpdateProfile;
use App\Support\Data\Admin\Permissions\CreatePermissionData;
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
use Spatie\LaravelData\Data;

dataset('backend_action_classes', [
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
    AuthenticatedUserData::class,
    SharedAuthData::class,
    UpdateProfileData::class,
]);

it('keeps admin and settings write orchestration in final action classes with typed handle signatures', function (string $actionClass): void {
    $reflection = new ReflectionClass($actionClass);

    expect($reflection->isFinal())->toBeTrue();
    expect($reflection->hasMethod('handle'))->toBeTrue();

    /** @var ReflectionMethod $handleMethod */
    $handleMethod = $reflection->getMethod('handle');

    expect($handleMethod->isPublic())->toBeTrue();
    expect($handleMethod->hasReturnType())->toBeTrue();

    foreach ($handleMethod->getParameters() as $parameter) {
        expect($parameter->hasType())->toBeTrue();
    }
})->with('backend_action_classes');

it('uses spatie data objects for non-trivial admin and shared payload contracts', function (string $dataClass): void {
    $reflection = new ReflectionClass($dataClass);

    expect($reflection->isFinal())->toBeTrue();
    expect($reflection->isSubclassOf(Data::class))->toBeTrue();
})->with('backend_data_classes');
