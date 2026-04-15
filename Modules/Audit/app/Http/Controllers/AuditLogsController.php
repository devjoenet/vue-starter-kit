<?php

declare(strict_types=1);

namespace Modules\Audit\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Audit\Actions\IndexAuditLogs;
use Modules\Audit\Http\Requests\IndexAuditLogsRequest;

final class AuditLogsController extends Controller
{
    #[Authorize('audit_logs.view')]
    public function index(IndexAuditLogsRequest $request): Response
    {
        return Inertia::render('Audit/Index', IndexAuditLogs::handle($request));
    }
}
