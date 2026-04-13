<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Audit\Actions\GetAuditHistoryItems;
use App\Modules\IAM\Permissions\Actions\CreatePermission;
use App\Modules\IAM\Permissions\Actions\DeletePermission;
use App\Modules\IAM\Permissions\Actions\IndexPermissions;
use App\Modules\IAM\Permissions\Actions\UpdatePermission;
use App\Modules\IAM\Permissions\Contracts\PermissionFilterOptionsProvider;
use App\Modules\IAM\Permissions\Contracts\PermissionGroupCatalogContract;
use App\Modules\IAM\Permissions\DTOs\CreatePermissionData;
use App\Modules\IAM\Permissions\DTOs\PermissionItemData;
use App\Modules\IAM\Permissions\DTOs\UpdatePermissionData;
use App\Modules\IAM\Permissions\Models\Permission;
use App\Modules\IAM\Permissions\Requests\StorePermissionRequest;
use App\Modules\IAM\Permissions\Requests\UpdatePermissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

final class PermissionsController extends Controller
{
    public function __construct(private readonly PermissionGroupCatalogContract $catalog) {}

    #[Authorize('permissions.view')]
    public function index(Request $request, PermissionFilterOptionsProvider $filters): Response
    {
        return Inertia::render('admin/Permissions/Index', IndexPermissions::handle($request, $this->catalog, $filters));
    }

    #[Authorize('permissions.create')]
    public function create(): Response
    {
        return Inertia::render('admin/Permissions/Create', [
            'groups' => $this->catalog->options(),
        ]);
    }

    #[Authorize('permissions.create')]
    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $permission = CreatePermission::handle(CreatePermissionData::fromRequest($request), $this->catalog);

        return $this->redirectRouteWithSuccess(
            'admin.permissions.edit',
            $permission,
            'Permission '.$permission->label.' created.',
        );
    }

    #[Authorize('permissions.update')]
    public function edit(Permission $permission): Response
    {
        $permission->load('permissionGroup');

        return Inertia::render('admin/Permissions/Edit', [
            'permission' => PermissionItemData::fromModel($permission),
            'auditHistory' => Inertia::defer(fn (): Collection => GetAuditHistoryItems::handle($permission)),
            'groups' => $this->catalog->options(),
        ]);
    }

    #[Authorize('permissions.update')]
    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        UpdatePermission::handle($permission, UpdatePermissionData::fromRequest($request), $this->catalog);

        return $request->boolean('quiet_success') ? back() : $this->backWithSuccess('Permission '.$permission->label.'  updated.');
    }

    #[Authorize('permissions.delete')]
    public function destroy(Permission $permission): RedirectResponse
    {
        DeletePermission::handle($permission);

        return $this->redirectRouteWithSuccess('admin.permissions.index', [], 'Permission '.$permission->label.'  deleted.');
    }
}
