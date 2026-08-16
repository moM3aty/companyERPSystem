<?php
// Path: app/Core/Audit/Http/Controllers/AuditLogController.php

declare(strict_types=1);

namespace App\Core\Audit\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Audit\EntityHistory;

/**
 * Enterprise API Controller: Audit Trail & Activity Logs
 * واجهة الإدارة العليا والمراجعين (Auditors) لمراقبة من قام بتعديل ماذا ومتى.
 */
class AuditLogController extends Controller
{
    protected EntityHistory $historyService;
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(EntityHistory $historyService, Gate $gate, TenantContext $tenant)
    {
        $this->historyService = $historyService;
        $this->gate = $gate;
        $this->tenant = $tenant;

        $this->middleware(['api', 'auth', 'tenant']);
    }

    /**
     * جلب الخط الزمني (Timeline) لكيان محدد (مثال: التعديلات التي تمت على فاتورة معينة).
     */
    public function entityTimeline(string $entityType, int $entityId): JsonResponse
    {
        // يتطلب صلاحية تدقيق عليا
        $this->gate->authorize('administration', 'audit_logs', 'view');

        $timeline = $this->historyService->getTimeline($entityType, $entityId);

        return ApiResponse::success($timeline, 'Entity audit timeline retrieved successfully.');
    }
}