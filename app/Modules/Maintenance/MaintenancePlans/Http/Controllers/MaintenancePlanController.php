<?php
// Path: app/Modules/Maintenance/MaintenancePlans/Http/Controllers/MaintenancePlanController.php

declare(strict_types=1);

namespace App\Modules\Maintenance\MaintenancePlans\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Maintenance\Application\MaintenanceService;
use App\Modules\Maintenance\MaintenancePlans\Http\Requests\StoreMaintenancePlanRequest;

/**
 * Enterprise API Controller: Maintenance Plans
 */
class MaintenancePlanController extends Controller
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

    public function store(Request $request, StoreMaintenancePlanRequest $validator): JsonResponse
    {
        $this->gate->authorize('maintenance', 'plans', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $planId = $this->service->createPlan($validatedData, $companyId, $userId);

        return ApiResponse::created(['plan_id' => $planId], 'Maintenance Plan scheduled successfully.');
    }
}