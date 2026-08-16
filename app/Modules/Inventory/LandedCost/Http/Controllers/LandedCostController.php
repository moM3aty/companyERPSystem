<?php
// Path: app/Modules/Inventory/LandedCost/Http/Controllers/LandedCostController.php

declare(strict_types=1);

namespace App\Modules\Inventory\LandedCost\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Auth\AuthManager;
use App\Core\Security\InputGuard;
use App\Modules\Inventory\LandedCost\Application\LandedCostService;
use App\Modules\Inventory\LandedCost\Http\Requests\StoreLandedCostRequest;

/**
 * Enterprise API Controller: Landed Costs
 */
class LandedCostController extends Controller
{
    protected LandedCostService $landedCostService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected AuthManager $auth;
    protected InputGuard $inputGuard;

    public function __construct(
        LandedCostService $landedCostService,
        Gate $gate,
        TenantContext $tenant,
        AuthManager $auth,
        InputGuard $inputGuard
    ) {
        $this->landedCostService = $landedCostService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->auth = $auth;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StoreLandedCostRequest $validator): JsonResponse
    {
        $this->gate->authorize('inventory', 'landed_costs', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $userId = $this->auth->user()->id;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $landedCostId = $this->landedCostService->applyCost($validatedData, $companyId, $userId);

        return ApiResponse::created(['landed_cost_id' => $landedCostId], 'Landed costs successfully allocated and inventory valuations updated.');
    }
}