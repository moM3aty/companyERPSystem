<?php
// Path: app/Modules/Treasury/Reconciliation/Http/Controllers/ReconciliationController.php

declare(strict_types=1);

namespace App\Modules\Treasury\Reconciliation\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Modules\Treasury\Reconciliation\Application\ReconciliationService;

/**
 * Enterprise API Controller: Bank Reconciliation
 */
class ReconciliationController extends Controller
{
    protected ReconciliationService $reconciliationService;
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(
        ReconciliationService $reconciliationService,
        Gate $gate,
        TenantContext $tenant
    ) {
        $this->reconciliationService = $reconciliationService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function autoMatch(Request $request, int $statementId): JsonResponse
    {
        $this->gate->authorize('treasury', 'reconciliation', 'execute');
        $companyId = $this->tenant->requireTenant()->companyId;

        $stats = $this->reconciliationService->autoMatch($statementId, $companyId);

        return ApiResponse::success($stats, 'Auto-match algorithm executed successfully.');
    }
}