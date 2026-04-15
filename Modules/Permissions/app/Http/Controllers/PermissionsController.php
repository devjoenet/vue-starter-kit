<?php

declare(strict_types=1);

namespace Modules\Permissions\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Audit\Actions\GetAuditHistoryItems;
use Modules\Permissions\Actions\CreatePermission;
use Modules\Permissions\Actions\DeletePermission;
use Modules\Permissions\Actions\IndexPermissions;
use Modules\Permissions\Actions\UpdatePermission;
use Modules\Permissions\Contracts\PermissionFilterOptionsProvider;
use Modules\Permissions\Contracts\PermissionGroupCatalogContract;
use Modules\Permissions\DTOs\CreatePermissionData;
use Modules\Permissions\DTOs\PermissionItemData;
use Modules\Permissions\DTOs\UpdatePermissionData;
use Modules\Permissions\Http\Requests\StorePermissionRequest;
use Modules\Permissions\Http\Requests\UpdatePermissionRequest;
use Modules\Permissions\Models\Permission;

final class PermissionsController extends Controller
{
    public function __construct(private readonly PermissionGroupCatalogContract $catalog) {}

    #[Authorize('permissions.view')]
    public function index(Request $request, PermissionFilterOptionsProvider $filters): Response
    {
        return Inertia::render('Permissions/Index', IndexPermissions::handle($request, $this->catalog, $filters));
    }

    #[Authorize('permissions.create')]
    public function create(): Response
    {
        return Inertia::render('Permissions/Create', [
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

        return Inertia::render('Permissions/Edit', [
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
