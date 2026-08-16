<?php
// Path: app/Modules/AdvancedPricing/Controllers/ContractPricingController.php

declare(strict_types=1);

namespace App\Modules\AdvancedPricing\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\AdvancedPricing\Services\ContractPricingService;

/**
 * Enterprise API Controller: Contract Pricing (B2B)
 */
class ContractPricingController extends Controller
{
    protected ContractPricingService $contractService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        ContractPricingService $contractService,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->contractService = $contractService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request): JsonResponse
    {
        $this->gate->authorize('advanced_pricing', 'contracts', 'create');
        
        $companyId = $this->tenant->requireTenant()->companyId;
        $cleanData = $this->inputGuard->getCleanPayload($request);

        // Validation assumed to be processed here via internal DTO or standard Factory
        $contractId = $this->contractService->createContract($cleanData, $companyId);

        return ApiResponse::created(['contract_id' => $contractId], 'B2B Customer Pricing Contract successfully activated.');
    }
}