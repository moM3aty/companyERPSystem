<?php
// Path: app/Modules/Sales/Promotions/Http/Controllers/PromotionController.php

declare(strict_types=1);

namespace App\Modules\Sales\Promotions\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Authorization\Gate;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;
use App\Modules\Sales\Promotions\Application\PromotionService;
use App\Modules\Sales\Promotions\Http\Requests\StorePromotionRequest;

class PromotionController extends Controller
{
    protected PromotionService $promotionService;
    protected Gate $gate;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        PromotionService $promotionService,
        Gate $gate,
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->promotionService = $promotionService;
        $this->gate = $gate;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function store(Request $request, StorePromotionRequest $validator): JsonResponse
    {
        $this->gate->authorize('sales', 'promotions', 'create');
        $companyId = $this->tenant->requireTenant()->companyId;

        $cleanData = $this->inputGuard->getCleanPayload($request);
        $validatedData = $validator->validate($cleanData, $companyId);

        $promotionId = $this->promotionService->createPromotion($validatedData, $companyId);

        return ApiResponse::created(['promotion_id' => $promotionId], 'Sales Promotion successfully activated.');
    }
}