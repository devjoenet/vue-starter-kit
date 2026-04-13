<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Audit\Actions\IndexAuditLogs;
use App\Modules\Audit\Requests\IndexAuditLogsRequest;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Inertia\Inertia;
use Inertia\Response;

final class AuditLogsController extends Controller
{
    #[Authorize('audit_logs.view')]
    public function index(IndexAuditLogsRequest $request): Response
    {
        return Inertia::render('admin/AuditLogs/Index', IndexAuditLogs::handle($request));
    }
}
