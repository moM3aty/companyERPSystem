<?php
// Path: app/Modules/AdvancedPricing/Controllers/PricingController.php
declare(strict_types=1);

namespace App\Modules\AdvancedPricing\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Modules\AdvancedPricing\Services\PriceCalculationService;
use App\Core\Tenant\TenantContext;
use App\Core\Security\InputGuard;

class PricingController extends Controller
{
    protected PriceCalculationService $pricingService;
    protected TenantContext $tenant;
    protected InputGuard $inputGuard;

    public function __construct(
        PriceCalculationService $pricingService, 
        TenantContext $tenant,
        InputGuard $inputGuard
    ) {
        $this->pricingService = $pricingService;
        $this->tenant = $tenant;
        $this->inputGuard = $inputGuard;
        $this->middleware(['api', 'auth', 'tenant']);
    }

    public function calculateQuote(Request $request): JsonResponse
    {
        $data = $this->inputGuard->getCleanPayload($request);
        $companyId = $this->tenant->requireTenant()->companyId;

        $quote = $this->pricingService->calculateFinalCartPrice(
            $data['items'] ?? [],
            (int)($data['customer_id'] ?? 0),
            $companyId
        );

        return ApiResponse::success(['quote' => $quote], 'Pricing calculated successfully using Advanced Pricing Engine.');
    }
}