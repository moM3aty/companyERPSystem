<?php
// Path: app/Modules/SupplyChain/Controllers/DemandController.php

declare(strict_types=1);

namespace App\Modules\SupplyChain\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Api\ApiError;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Modules\SupplyChain\Services\DemandPlanningService;

/**
 * Enterprise API Controller: Demand Planning
 */
class DemandController extends Controller
{
    protected DemandPlanningService $demandService;
    protected Gate $gate;
    protected TenantContext $tenant;

    public function __construct(
        DemandPlanningService $demandService,
        Gate $gate,
        TenantContext $tenant
    ) {
        $this->demandService = $demandService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function runForecast(Request $request): JsonResponse
    {
        $this->gate->authorize('supply_chain', 'demand_planning', 'execute');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $targetPeriod = $request->post('target_period'); // YYYY-MM

        if (!$targetPeriod || !preg_match('/^20\d{2}-(0[1-9]|1[0-2])$/', $targetPeriod)) {
            return ApiError::error("Invalid target_period. Format must be YYYY-MM.", 422);
        }

        $count = $this->demandService->generateForecast($companyId, $targetPeriod);

        return ApiResponse::success(
            ['forecasts_generated' => $count], 
            "Demand planning engine executed successfully. Forecasts generated for {$count} products based on 90-day moving average."
        );
    }
}