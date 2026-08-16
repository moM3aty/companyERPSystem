<?php
// Path: app/Modules/Maintenance/WorkOrders/Http/Controllers/WorkOrderController.php

declare(strict_types=1);

namespace App\Modules\Maintenance\WorkOrders\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Maintenance\Application\MaintenanceService;
use App\Modules\Maintenance\WorkOrders\Http\Requests\StoreWorkOrderRequest;

/**
 * Enterprise API Controller: Work Orders
 */
class WorkOrderController extends Controller
{
    protected MaintenanceService $service;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        MaintenanceService $service,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->service = $service;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreWorkOrderRequest $validator): JsonResponse
    {
        $this->gate->authorize('maintenance', 'work_orders', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $orderId = $this->service->createWorkOrder($validatedData, $companyId, $userId);

        return ApiResponse::created(['work_order_id' => $orderId], 'Maintenance Work Order issued successfully.');
    }

    public function complete(Request $request, int $id): JsonResponse
    {
        $this->gate->authorize('maintenance', 'work_orders', 'complete');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $actualCost = (float) ($cleanData['actual_cost'] ?? 0);

        $this->service->completeWorkOrder($id, $actualCost, $companyId);

        return ApiResponse::success(null, 'Work Order completed. Costs and assets updated.');
    }
}