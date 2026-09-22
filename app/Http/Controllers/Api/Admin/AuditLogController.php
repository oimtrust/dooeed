<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AuditIndexRequest;
use App\Http\Resources\Admin\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuditLogController extends Controller
{
    public function index(AuditIndexRequest $request): AnonymousResourceCollection
    {
        $logs = AuditLog::query()->with(['admin', 'targetUser'])
            ->when($request->validated('admin_id'), fn ($query, $id) => $query->where('admin_id', $id))
            ->when($request->validated('target_user_id'), fn ($query, $id) => $query->where('target_user_id', $id))
            ->latest()->orderByDesc('id')->paginate(20)->withQueryString();

        return AuditLogResource::collection($logs);
    }
}
