<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Audit\Actions\IndexAuditLogs;
use App\Modules\Audit\Requests\IndexAuditLogsRequest;
use Illuminate\Http\JsonResponse;

final class AuditLogsController extends Controller
{
    public function index(IndexAuditLogsRequest $request): JsonResponse
    {
        return response()->json(IndexAuditLogs::handle($request));
    }
}
